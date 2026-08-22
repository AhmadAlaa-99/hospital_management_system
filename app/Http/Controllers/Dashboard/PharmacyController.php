<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Medicine;
use App\Models\Patient;
use App\Models\PharmacyDispensing;
use App\Models\Prescription;
use App\Services\AuditLogService;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PharmacyController extends Controller
{
    public function index()
    {
        $medicines = Medicine::orderBy('name')->paginate(20);
        $lowStock = Medicine::whereColumn('quantity', '<=', 'min_stock_level')->where('is_active', true)->get();
        $recentDispensings = PharmacyDispensing::with(['patient', 'medicine'])->latest()->limit(10)->get();

        return view('Dashboard.Pharmacy.index', compact('medicines', 'lowStock', 'recentDispensings'));
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

        $medicine->update($data);
        AuditLogService::log('medicine_updated', $medicine);

        session()->flash('edit');
        return back();
    }

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
            'prescription_id' => 'nullable|exists:prescriptions,id',
            'diagnostic_id' => 'nullable|exists:diagnostics,id',
            'quantity_dispensed' => 'required|integer|min:1',
        ]);

        DB::beginTransaction();
        try {
            $medicine = Medicine::lockForUpdate()->findOrFail($data['medicine_id']);

            if ($medicine->quantity < $data['quantity_dispensed']) {
                return back()->withErrors(['error' => 'الكمية المتاحة غير كافية. المتوفر: ' . $medicine->quantity]);
            }

            $total = $medicine->unit_price * $data['quantity_dispensed'];

            $dispensing = PharmacyDispensing::create([
                'patient_id' => $data['patient_id'],
                'medicine_id' => $medicine->id,
                'prescription_id' => $data['prescription_id'] ?? null,
                'diagnostic_id' => $data['diagnostic_id'] ?? null,
                'quantity_dispensed' => $data['quantity_dispensed'],
                'unit_price' => $medicine->unit_price,
                'total_price' => $total,
                'dispensed_by' => Auth::guard('admin')->id(),
                'dispensed_by_type' => 'admin',
            ]);

            $medicine->decrement('quantity', $data['quantity_dispensed']);

            AuditLogService::log('pharmacy_dispensed', $dispensing);

            if ($medicine->fresh()->isLowStock()) {
                NotificationService::notifyAdmin('تنبيه: مخزون منخفض للدواء ' . $medicine->name, route('pharmacy.index'));
            }

            NotificationService::notifyPatient(
                (int) $data['patient_id'],
                'تم صرف دواء: ' . $medicine->name . ' — المبلغ: ' . number_format($total, 2)
            );

            DB::commit();
            session()->flash('add');
            return redirect()->route('pharmacy.index');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function prescriptionsForPatient(Patient $patient)
    {
        $prescriptions = Prescription::whereHas('diagnostic', function ($q) use ($patient) {
            $q->where('patient_id', $patient->id);
        })->with('diagnostic')->latest()->get();

        return response()->json(['prescriptions' => $prescriptions]);
    }
}
