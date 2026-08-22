<?php

namespace App\Services;

use App\Models\Diagnostic;
use App\Models\FundAccount;
use App\Models\Medicine;
use App\Models\PatientAccount;
use App\Models\PharmacyDispensing;
use App\Models\PharmacyInvoice;
use App\Models\Prescription;
use App\Models\ReceiptAccount;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class PharmacyService
{
    /**
     * تشخيصات لها وصفة إلكترونية ولم تُصرف بالكامل بعد.
     */
    public static function pendingDiagnostics()
    {
        return Diagnostic::with(['patient', 'Doctor', 'prescriptions'])
            ->whereHas('prescriptions', fn ($q) => $q->where('is_dispensed', false))
            ->latest()
            ->get();
    }

    /**
     * مطابقة اسم الدواء من الوصفة مع مخزون الصيدلية.
     */
    public static function matchMedicine(string $prescriptionName): ?Medicine
    {
        $name = trim($prescriptionName);
        if ($name === '') {
            return null;
        }

        $medicine = Medicine::where('is_active', true)
            ->where(function ($q) use ($name) {
                $q->where('name', 'like', '%' . $name . '%')
                    ->orWhere('generic_name', 'like', '%' . $name . '%');
            })
            ->where('quantity', '>', 0)
            ->first();

        if ($medicine) {
            return $medicine;
        }

        // مطابقة جزئية: أول كلمة من اسم الدواء
        $firstWord = explode(' ', $name)[0] ?? $name;
        if (mb_strlen($firstWord) >= 3) {
            return Medicine::where('is_active', true)
                ->where('quantity', '>', 0)
                ->where(function ($q) use ($firstWord) {
                    $q->where('name', 'like', '%' . $firstWord . '%')
                        ->orWhere('generic_name', 'like', '%' . $firstWord . '%');
                })
                ->first();
        }

        return null;
    }

    /**
     * صرف وصفة إلكترونية كاملة وإنشاء فاتورة صيدلية.
     *
     * @param  array<int, array{prescription_id:int, medicine_id:int, quantity:int}>  $lines
     */
    public static function dispenseFromPrescription(
        Diagnostic $diagnostic,
        array $lines,
        int $dispensedBy,
        string $dispensedByType = 'admin',
        ?string $notes = null
    ): PharmacyInvoice {
        return DB::transaction(function () use ($diagnostic, $lines, $dispensedBy, $dispensedByType, $notes) {
            $invoice = PharmacyInvoice::create([
                'invoice_number' => PharmacyInvoice::generateNumber(),
                'patient_id' => $diagnostic->patient_id,
                'diagnostic_id' => $diagnostic->id,
                'doctor_id' => $diagnostic->doctor_id,
                'dispensed_by' => $dispensedBy,
                'dispensed_by_type' => $dispensedByType,
                'notes' => $notes,
                'issued_at' => now(),
            ]);

            $subtotal = 0;

            foreach ($lines as $line) {
                if (empty($line['prescription_id']) || empty($line['medicine_id']) || empty($line['quantity'])) {
                    continue;
                }

                $prescription = Prescription::where('id', $line['prescription_id'])
                    ->where('diagnostic_id', $diagnostic->id)
                    ->where('is_dispensed', false)
                    ->firstOrFail();

                $medicine = Medicine::lockForUpdate()->findOrFail($line['medicine_id']);
                $qty = (int) $line['quantity'];

                if ($medicine->quantity < $qty) {
                    throw new \RuntimeException('الكمية غير كافية للدواء: ' . $medicine->name);
                }

                $lineTotal = $medicine->unit_price * $qty;

                PharmacyDispensing::create([
                    'pharmacy_invoice_id' => $invoice->id,
                    'patient_id' => $diagnostic->patient_id,
                    'diagnostic_id' => $diagnostic->id,
                    'prescription_id' => $prescription->id,
                    'medicine_id' => $medicine->id,
                    'quantity_dispensed' => $qty,
                    'unit_price' => $medicine->unit_price,
                    'total_price' => $lineTotal,
                    'dispensed_by' => $dispensedBy,
                    'dispensed_by_type' => $dispensedByType,
                    'dispensed_at' => now(),
                ]);

                $medicine->decrement('quantity', $qty);

                $prescription->update([
                    'is_dispensed' => true,
                    'dispensed_at' => now(),
                    'medicine_id' => $medicine->id,
                ]);

                $subtotal += $lineTotal;

                if ($medicine->fresh()->isLowStock()) {
                    NotificationService::notifyAdmin(
                        'تنبيه مخزون منخفض: ' . $medicine->name . ' (متبقي: ' . $medicine->quantity . ')',
                        route('pharmacy.index')
                    );
                }
            }

            $invoice->update([
                'subtotal' => $subtotal,
                'total_amount' => $subtotal,
            ]);

            // تسجيل سند قبض — فاتورة صيدلية
            if ($subtotal > 0) {
                $receipt = ReceiptAccount::create([
                    'date' => now()->toDateString(),
                    'patient_id' => $diagnostic->patient_id,
                    'amount' => $subtotal,
                    'description' => 'فاتورة صيدلية ' . $invoice->invoice_number . ' — وصفة إلكترونية',
                ]);

                FundAccount::create([
                    'date' => now()->toDateString(),
                    'receipt_id' => $receipt->id,
                    'Debit' => $subtotal,
                    'credit' => 0,
                ]);

                PatientAccount::create([
                    'date' => now()->toDateString(),
                    'patient_id' => $diagnostic->patient_id,
                    'receipt_id' => $receipt->id,
                    'Debit' => 0,
                    'credit' => $subtotal,
                ]);
            }

            AuditLogService::log('pharmacy_invoice_created', $invoice);

            NotificationService::notifyPatient(
                (int) $diagnostic->patient_id,
                'تم صرف وصفتك من صيدلية العيادة — فاتورة ' . $invoice->invoice_number . ' — المبلغ: ' . number_format($subtotal, 2)
            );

            return $invoice->fresh(['items.medicine', 'items.prescription', 'patient', 'doctor', 'diagnostic']);
        });
    }

    public static function lowStockMedicines(): Collection
    {
        return Medicine::where('is_active', true)
            ->whereColumn('quantity', '<=', 'min_stock_level')
            ->orderBy('quantity')
            ->get();
    }
}
