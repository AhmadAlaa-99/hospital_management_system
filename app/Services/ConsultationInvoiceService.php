<?php

namespace App\Services;

use App\Events\CreateInvoice;
use App\Models\Appointment;
use App\Models\FundAccount;
use App\Models\Group;
use App\Models\Invoice;
use App\Models\Patient;
use App\Models\PatientAccount;
use App\Models\Service;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ConsultationInvoiceService
{
    /**
     * فاتورة خدمة مفردة — يستخدمها الطبيب والاستقbal والكشف التلقائي.
     */
    public function createServiceInvoice(array $data, bool $notifyDoctor = false): Invoice
    {
        $service = Service::findOrFail($data['Service_id']);
        $patientId = (int) $data['patient_id'];
        $doctorId = (int) $data['doctor_id'];
        $sectionId = (int) $data['section_id'];
        $price = (float) ($data['price'] ?? $service->price);
        $discount = array_key_exists('discount_value', $data)
            ? (float) $data['discount_value']
            : $this->insuranceDiscount($patientId, $price);
        $taxRate = (float) ($data['tax_rate'] ?? 17);
        $paymentType = (int) ($data['type'] ?? 1);
        $subtotal = $price - $discount;
        $taxValue = round($subtotal * ($taxRate / 100), 2);
        $total = round($subtotal + $taxValue, 2);

        return DB::transaction(function () use (
            $data, $service, $patientId, $doctorId, $sectionId,
            $price, $discount, $taxRate, $taxValue, $total, $paymentType, $notifyDoctor
        ) {
            $invoice = Invoice::create([
                'invoice_type' => 1,
                'invoice_status' => (int) ($data['invoice_status'] ?? 1),
                'invoice_date' => $data['invoice_date'] ?? now()->toDateString(),
                'patient_id' => $patientId,
                'doctor_id' => $doctorId,
                'section_id' => $sectionId,
                'appointment_id' => $data['appointment_id'] ?? null,
                'Service_id' => $service->id,
                'price' => $price,
                'discount_value' => $discount,
                'tax_rate' => $taxRate,
                'tax_value' => $taxValue,
                'total_with_tax' => $total,
                'type' => $paymentType,
            ]);

            InsuranceClaimService::createFromInvoice($invoice);
            $this->syncPaymentAccounts($invoice, $paymentType, $total);

            if ($notifyDoctor) {
                $patient = Patient::find($patientId);
                NotificationService::notifyDoctor(
                    $doctorId,
                    'كشف جديد : ' . optional($patient)->name
                );

                event(new CreateInvoice([
                    'patient' => $patientId,
                    'invoice_id' => $invoice->id,
                    'doctor_id' => $doctorId,
                ]));
            }

            return $invoice;
        });
    }

    /**
     * فاتورة مجموعة خدمات — مثل لوحة الأدمن.
     */
    public function createGroupInvoice(array $data, bool $notifyDoctor = false): Invoice
    {
        $group = Group::findOrFail($data['Group_id']);
        $patientId = (int) $data['patient_id'];
        $doctorId = (int) $data['doctor_id'];
        $sectionId = (int) $data['section_id'];
        $price = (float) ($data['price'] ?? $group->Total_before_discount);
        $discount = array_key_exists('discount_value', $data)
            ? (float) $data['discount_value']
            : $this->groupDiscountWithInsurance($patientId, $group, $price);
        $taxRate = (float) ($data['tax_rate'] ?? $group->tax_rate ?? 17);
        $paymentType = (int) ($data['type'] ?? 1);
        $subtotal = $price - $discount;
        $taxValue = round($subtotal * ($taxRate / 100), 2);
        $total = round($subtotal + $taxValue, 2);

        return DB::transaction(function () use (
            $data, $group, $patientId, $doctorId, $sectionId,
            $price, $discount, $taxRate, $taxValue, $total, $paymentType, $notifyDoctor
        ) {
            $invoice = Invoice::create([
                'invoice_type' => 2,
                'invoice_status' => (int) ($data['invoice_status'] ?? 1),
                'invoice_date' => $data['invoice_date'] ?? now()->toDateString(),
                'patient_id' => $patientId,
                'doctor_id' => $doctorId,
                'section_id' => $sectionId,
                'appointment_id' => $data['appointment_id'] ?? null,
                'Group_id' => $group->id,
                'price' => $price,
                'discount_value' => $discount,
                'tax_rate' => $taxRate,
                'tax_value' => $taxValue,
                'total_with_tax' => $total,
                'type' => $paymentType,
            ]);

            InsuranceClaimService::createFromInvoice($invoice);
            $this->syncPaymentAccounts($invoice, $paymentType, $total);

            if ($notifyDoctor) {
                $patient = Patient::find($patientId);
                NotificationService::notifyDoctor(
                    $doctorId,
                    'فاتورة مجموعة خدمات : ' . optional($patient)->name
                );

                event(new CreateInvoice([
                    'patient' => $patientId,
                    'invoice_id' => $invoice->id,
                    'doctor_id' => $doctorId,
                ]));
            }

            return $invoice;
        });
    }

    /**
     * @return array{invoice: Invoice|null, created: bool}
     */
    public function ensureForAppointment(Appointment $appointment, bool $notifyDoctor = true, ?int $fallbackPatientId = null): array
    {
        $service = $this->defaultConsultationService();
        if (!$service || !$appointment->doctor_id || !$appointment->section_id) {
            return ['invoice' => null, 'created' => false];
        }

        $existing = Invoice::where('appointment_id', $appointment->id)
            ->where('Service_id', $service->id)
            ->first();

        if ($existing) {
            return ['invoice' => $existing, 'created' => false];
        }

        $patientId = $this->resolvePatientId($appointment, $fallbackPatientId);
        if (!$patientId) {
            Log::warning('ConsultationInvoiceService: no patient for appointment #' . $appointment->id);

            return ['invoice' => null, 'created' => false];
        }

        $invoice = $this->createServiceInvoice([
            'patient_id' => $patientId,
            'doctor_id' => $appointment->doctor_id,
            'section_id' => $appointment->section_id,
            'appointment_id' => $appointment->id,
            'Service_id' => $service->id,
            'type' => 1,
        ], $notifyDoctor);

        return ['invoice' => $invoice, 'created' => true];
    }

    protected function syncPaymentAccounts(Invoice $invoice, int $paymentType, float $total): void
    {
        if ($paymentType === 1) {
            $fund = FundAccount::firstOrNew(['invoice_id' => $invoice->id]);
            $fund->date = now()->toDateString();
            $fund->invoice_id = $invoice->id;
            $fund->Debit = $total;
            $fund->credit = 0;
            $fund->save();
            PatientAccount::where('invoice_id', $invoice->id)->delete();

            return;
        }

        $patientAccount = PatientAccount::firstOrNew(['invoice_id' => $invoice->id]);
        $patientAccount->date = now()->toDateString();
        $patientAccount->invoice_id = $invoice->id;
        $patientAccount->patient_id = $invoice->patient_id;
        $patientAccount->Debit = $total;
        $patientAccount->credit = 0;
        $patientAccount->save();
        FundAccount::where('invoice_id', $invoice->id)->delete();
    }

    protected function resolvePatientId(Appointment $appointment, ?int $fallbackPatientId = null): ?int
    {
        if ($appointment->email && !str_contains($appointment->email, '@hms.local')) {
            $patient = Patient::where('email', $appointment->email)->first();
            if ($patient) {
                return (int) $patient->id;
            }
        }

        if ($appointment->phone) {
            $patient = Patient::where('Phone', $appointment->phone)->first();
            if ($patient) {
                return (int) $patient->id;
            }
        }

        if ($fallbackPatientId) {
            return (int) $fallbackPatientId;
        }

        return null;
    }

    protected function defaultConsultationService(): ?Service
    {
        $service = Service::whereHas('translations', function ($q) {
            $q->where('name', 'كشف');
        })->first();

        return $service ?: Service::orderBy('id')->first();
    }

    protected function insuranceDiscount(int $patientId, float $price): float
    {
        if ($price <= 0) {
            return 0;
        }

        $patient = Patient::with('insurance')->find($patientId);
        if (!$patient || !$patient->insurance || (int) $patient->insurance->status !== 1) {
            return 0;
        }

        return round($price * ((float) $patient->insurance->discount_percentage / 100), 2);
    }

    protected function groupDiscountWithInsurance(int $patientId, Group $group, float $price): float
    {
        $baseDiscount = (float) $group->discount_value;

        if ($price <= 0) {
            return $baseDiscount;
        }

        $patient = Patient::with('insurance')->find($patientId);
        if (!$patient || !$patient->insurance || (int) $patient->insurance->status !== 1) {
            return $baseDiscount;
        }

        $priceAfterGroupDiscount = $price - $baseDiscount;
        $insuranceDiscount = round(
            $priceAfterGroupDiscount * ((float) $patient->insurance->discount_percentage / 100),
            2
        );

        return $baseDiscount + $insuranceDiscount;
    }
}
