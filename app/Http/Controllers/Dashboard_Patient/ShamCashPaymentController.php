<?php

namespace App\Http\Controllers\Dashboard_Patient;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\ShamCashPayment;
use App\Models\SiteSetting;
use App\Services\AuditLogService;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ShamCashPaymentController extends Controller
{
    public function show(Invoice $invoice)
    {
        $this->authorizePatientInvoice($invoice);

        $settings = SiteSetting::current();
        $latestPayment = ShamCashPayment::where('invoice_id', $invoice->id)
            ->latest()
            ->first();

        return view('Dashboard.dashboard_patient.sham_cash.pay', compact('invoice', 'settings', 'latestPayment'));
    }

    public function store(Request $request, Invoice $invoice)
    {
        $this->authorizePatientInvoice($invoice);

        if (!in_array($invoice->payment_status, ['unpaid', 'rejected'], true)) {
            return back()->withErrors(['error' => 'لا يمكن إرسال دفع لهذه الفاتورة في حالتها الحالية.']);
        }

        $settings = SiteSetting::current();
        if (!$settings->sham_cash_enabled || !$settings->sham_cash_wallet) {
            return back()->withErrors(['error' => 'الدفع عبر شام كاش غير متاح حالياً.']);
        }

        $data = $request->validate([
            'receipt' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'transaction_reference' => 'nullable|string|max:100',
            'patient_notes' => 'nullable|string|max:500',
        ], [
            'receipt.required' => 'يرجى رفع إيصال الدفع.',
            'receipt.mimes' => 'صيغة الإيصال: JPG, PNG أو PDF.',
        ]);

        $path = $request->file('receipt')->store(
            'sham-cash/receipts/' . Auth::guard('patient')->id(),
            'public'
        );

        $payment = ShamCashPayment::create([
            'invoice_id' => $invoice->id,
            'patient_id' => Auth::guard('patient')->id(),
            'amount' => $invoice->total_with_tax,
            'status' => 'pending_review',
            'receipt_path' => $path,
            'transaction_reference' => $data['transaction_reference'] ?? null,
            'patient_notes' => $data['patient_notes'] ?? null,
        ]);

        $invoice->update(['payment_status' => 'pending_review']);

        AuditLogService::log('sham_cash_payment_submitted', $payment);
        NotificationService::notifyAdmin(
            'دفع شام كاش بانتظار المراجعة — فاتورة #' . $invoice->id,
            route('sham-cash-payments.index')
        );

        session()->flash('add');
        return redirect()->route('invoices.patient');
    }

    protected function authorizePatientInvoice(Invoice $invoice): void
    {
        if ($invoice->patient_id !== Auth::guard('patient')->id()) {
            abort(403);
        }
    }
}
