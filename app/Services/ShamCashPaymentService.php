<?php

namespace App\Services;

use App\Models\FundAccount;
use App\Models\Invoice;
use App\Models\PatientAccount;
use App\Models\ReceiptAccount;
use App\Models\ShamCashPayment;
use Illuminate\Support\Facades\DB;

class ShamCashPaymentService
{
    public static function approve(ShamCashPayment $payment, int $adminId, ?string $adminNotes = null): void
    {
        if ($payment->status !== 'pending_review') {
            throw new \RuntimeException('لا يمكن اعتماد هذه العملية في حالتها الحالية.');
        }

        DB::transaction(function () use ($payment, $adminId, $adminNotes) {
            $invoice = Invoice::findOrFail($payment->invoice_id);

            $payment->update([
                'status' => 'approved',
                'admin_notes' => $adminNotes,
                'reviewed_by' => $adminId,
                'reviewed_at' => now(),
            ]);

            $invoice->update(['payment_status' => 'paid']);

            $description = 'دفع شام كاش — فاتورة #' . $invoice->id;
            if ($payment->transaction_reference) {
                $description .= ' — مرجع: ' . $payment->transaction_reference;
            }

            $receipt = ReceiptAccount::create([
                'date' => now()->toDateString(),
                'patient_id' => $payment->patient_id,
                'amount' => $payment->amount,
                'description' => $description,
            ]);

            FundAccount::create([
                'date' => now()->toDateString(),
                'invoice_id' => $invoice->id,
                'receipt_id' => $receipt->id,
                'Debit' => $payment->amount,
                'credit' => 0,
            ]);

            PatientAccount::create([
                'date' => now()->toDateString(),
                'patient_id' => $payment->patient_id,
                'receipt_id' => $receipt->id,
                'Debit' => 0,
                'credit' => $payment->amount,
            ]);

            AuditLogService::log('sham_cash_payment_approved', $payment);
            NotificationService::notifyPatient(
                (int) $payment->patient_id,
                'تم اعتماد دفع شام كاش للفاتورة #' . $invoice->id
            );
        });
    }

    public static function reject(ShamCashPayment $payment, int $adminId, ?string $adminNotes = null): void
    {
        if ($payment->status !== 'pending_review') {
            throw new \RuntimeException('لا يمكن رفض هذه العملية في حالتها الحالية.');
        }

        $payment->update([
            'status' => 'rejected',
            'admin_notes' => $adminNotes,
            'reviewed_by' => $adminId,
            'reviewed_at' => now(),
        ]);

        Invoice::where('id', $payment->invoice_id)->update(['payment_status' => 'rejected']);

        AuditLogService::log('sham_cash_payment_rejected', $payment);
        NotificationService::notifyPatient(
            (int) $payment->patient_id,
            'تم رفض إيصال الدفع للفاتورة #' . $payment->invoice_id . ' — يرجى إعادة المحاولة'
        );
    }
}
