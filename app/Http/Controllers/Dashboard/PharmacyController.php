<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Diagnostic;
use App\Models\Medicine;
use App\Models\Patient;
use App\Models\PharmacyDispensing;
use App\Models\PharmacyInvoice;
use App\Models\Prescription;
use App\Services\AuditLogService;
use App\Services\NotificationService;
use App\Services\PharmacyService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PharmacyController extends Controller
{
    public function index()
    {
        $medicines = Medicine::orderBy('name')->paginate(15);
        $lowStock = PharmacyService::lowStockMedicines();
        $pendingDiagnostics = PharmacyService::pendingDiagnostics();
        $recentInvoices = PharmacyInvoice::with('patient')->latest()->limit(8)->get();
        $recentDispensings = PharmacyDispensing::with(['patient', 'medicine'])->latest()->limit(5)->get();

        return view('Dashboard.Pharmacy.index', compact(
            'medicines', 'lowStock', 'pendingDiagnostics', 'recentInvoices', 'recentDispensings'
        ));
    }

    public function invoices()
    {
        $invoices = PharmacyInvoice::with(['patient', 'doctor'])->latest()->paginate(20);

        return view('Dashboard.Pharmacy.invoices', compact('invoices'));
    }

    public function showInvoice(PharmacyInvoice $pharmacyInvoice)
    {
        $pharmacyInvoice->load(['items.medicine', 'items.prescription', 'patient', 'doctor', 'diagnostic']);

        return view('Dashboard.Pharmacy.invoice_show', compact('pharmacyInvoice'));
    }

    public function printInvoice(PharmacyInvoice $pharmacyInvoice)
    {
        $pharmacyInvoice->load(['items.medicine', 'items.prescription', 'patient', 'doctor', 'diagnostic']);

        $pdf = Pdf::loadView('pdf.pharmacy-invoice', compact('pharmacyInvoice'))->setPaper('a5');

        return $pdf->download($pharmacyInvoice->invoice_number . '.pdf');
    }

    public function dispenseFromPrescription(Diagnostic $diagnostic)
    {
        $diagnostic->load(['patient', 'Doctor', 'prescriptions']);

        $lines = $diagnostic->prescriptions
            ->where('is_dispensed', false)
            ->map(function (Prescription $rx) {
                $matched = PharmacyService::matchMedicine($rx->medicine_name);

                return [
                    'prescription' => $rx,
                    'matched_medicine' => $matched,
                    'suggested_qty' => max(1, (int) ($rx->duration_days ? min($rx->duration_days, 30) : 1)),
                ];
            });

        if ($lines->isEmpty()) {
            return redirect()->route('pharmacy.index')->withErrors(['error' => 'لا توجد أدوية بانتظار الصرف في هذه الوصفة.']);
        }

        $allMedicines = Medicine::where('is_active', true)->where('quantity', '>', 0)->orderBy('name')->get();

        return view('Dashboard.Pharmacy.dispense_prescription', compact('diagnostic', 'lines', 'allMedicines'));
    }

    public function processPrescriptionDispense(Request $request, Diagnostic $diagnostic)
    {
        $request->validate([
            'lines' => 'required|array|min:1',
            'notes' => 'nullable|string|max:500',
        ]);

        $lines = collect($request->input('lines', []))
            ->filter(function ($line) {
                return !empty($line['enabled'])
                    && !empty($line['prescription_id'])
                    && !empty($line['medicine_id'])
                    && !empty($line['quantity']);
            })
            ->map(fn ($line) => [
                'prescription_id' => (int) $line['prescription_id'],
                'medicine_id' => (int) $line['medicine_id'],
                'quantity' => (int) $line['quantity'],
            ])
            ->values()
            ->all();

        if (empty($lines)) {
            return back()->withErrors(['error' => 'يرجى اختيار دواء واحد على الأقل للصرف.']);
        }

        try {
            $invoice = PharmacyService::dispenseFromPrescription(
                $diagnostic,
                $lines,
                (int) Auth::guard('admin')->id(),
                'admin',
                $request->notes
            );

            session()->flash('add');
            return redirect()->route('pharmacy.invoices.show', $invoice);
        } catch (\RuntimeException $e) {
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    public function storeMedicine(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:200',
            'generic_name' => 'nullable|string|max:200',
            'quantity' => 'required|integer|min:0',
            'unit_price' => 'required|numeric|min:0',
            'expiry_date' => 'nullable|date|after:today',
            'min_stock_level' => 'required|integer|min:1',
        ]);

        $medicine = Medicine::create($data);
        AuditLogService::log('medicine_created', $medicine);

        session()->flash('add');
        return back();
    }

    public function updateMedicine(Request $request, Medicine $medicine)
    {
        $data = $request->validate([
            'name' => 'required|string|max:200',
            'generic_name' => 'nullable|string|max:200',
            'quantity' => 'required|integer|min:0',
            'unit_price' => 'required|numeric|min:0',
            'expiry_date' => 'nullable|date',
            'min_stock_level' => 'required|integer|min:1',
            'is_active' => 'nullable|boolean',
        ]);

        $data['is_active'] = $request->boolean('is_active', true);
        $medicine->update($data);
        AuditLogService::log('medicine_updated', $medicine);

        session()->flash('edit');
        return back();
    }

    /** صرف يدوي لدواء واحد (بدون وصفة) */
    public function dispenseForm()
    {
        $medicines = Medicine::where('is_active', true)->where('quantity', '>', 0)->orderBy('name')->get();
        $patients = Patient::with('translations')->latest()->limit(100)->get();

        return view('Dashboard.Pharmacy.dispense', compact('medicines', 'patients'));
    }

    public function dispense(Request $request)
    {
        $data = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'medicine_id' => 'required|exists:medicines,id',
            'quantity_dispensed' => 'required|integer|min:1',
        ]);

        DB::beginTransaction();
        try {
            $medicine = Medicine::lockForUpdate()->findOrFail($data['medicine_id']);

            if ($medicine->quantity < $data['quantity_dispensed']) {
                return back()->withErrors(['error' => 'الكمية المتاحة غير كافية. المتوفر: ' . $medicine->quantity]);
            }

            $total = $medicine->unit_price * $data['quantity_dispensed'];

            $invoice = PharmacyInvoice::create([
                'invoice_number' => PharmacyInvoice::generateNumber(),
                'patient_id' => $data['patient_id'],
                'subtotal' => $total,
                'total_amount' => $total,
                'dispensed_by' => Auth::guard('admin')->id(),
                'notes' => 'صرف يدوي — بدون وصفة',
                'issued_at' => now(),
            ]);

            PharmacyDispensing::create([
                'pharmacy_invoice_id' => $invoice->id,
                'patient_id' => $data['patient_id'],
                'medicine_id' => $medicine->id,
                'quantity_dispensed' => $data['quantity_dispensed'],
                'unit_price' => $medicine->unit_price,
                'total_price' => $total,
                'dispensed_by' => Auth::guard('admin')->id(),
                'dispensed_by_type' => 'admin',
                'dispensed_at' => now(),
            ]);

            $medicine->decrement('quantity', $data['quantity_dispensed']);
            AuditLogService::log('pharmacy_dispensed', $invoice);

            if ($medicine->fresh()->isLowStock()) {
                NotificationService::notifyAdmin('تنبيه مخزون منخفض: ' . $medicine->name, route('pharmacy.index'));
            }

            NotificationService::notifyPatient(
                (int) $data['patient_id'],
                'تم صرف دواء من صيدلية العيادة — فاتورة ' . $invoice->invoice_number
            );

            DB::commit();
            session()->flash('add');
            return redirect()->route('pharmacy.invoices.show', $invoice);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function prescriptionsForPatient(Patient $patient)
    {
        $prescriptions = Prescription::whereHas('diagnostic', fn ($q) => $q->where('patient_id', $patient->id))
            ->where('is_dispensed', false)
            ->with('diagnostic')
            ->latest()
            ->get();

        return response()->json(['prescriptions' => $prescriptions]);
    }
}
