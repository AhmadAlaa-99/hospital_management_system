<?php

namespace App\Services;

use App\Models\Insurance;
use App\Models\InsuranceClaim;
use App\Models\Invoice;
use App\Models\Patient;

class InsuranceClaimService
{
    public static function createFromInvoice(Invoice $invoice): ?InsuranceClaim
    {
        $patient = Patient::with('insurance')->find($invoice->patient_id);
        if (!$patient || !$patient->insurance_id || !$patient->insurance || !(int) $patient->insurance->status) {
            return null;
        }

        if (InsuranceClaim::where('invoice_id', $invoice->id)->exists()) {
            return InsuranceClaim::where('invoice_id', $invoice->id)->first();
        }

        $insurance = $patient->insurance;
        $total = (float) $invoice->total_with_tax;
        $discount = (float) ($invoice->discount_value ?? 0);
        $companyRate = (float) $insurance->Company_rate;
        $companyAmount = round($total * ($companyRate / 100), 2);
        $patientAmount = round($total - $companyAmount, 2);

        return InsuranceClaim::create([
            'patient_id' => $patient->id,
            'insurance_id' => $insurance->id,
            'invoice_id' => $invoice->id,
            'total_amount' => $total,
            'discount_amount' => $discount,
            'company_amount' => $companyAmount,
            'patient_amount' => $patientAmount,
            'status' => 'pending',
            'claim_date' => now()->toDateString(),
        ]);
    }
}
