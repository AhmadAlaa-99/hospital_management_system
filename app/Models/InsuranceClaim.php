<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InsuranceClaim extends Model
{
    protected $fillable = [
        'patient_id', 'insurance_id', 'invoice_id', 'total_amount', 'discount_amount',
        'company_amount', 'patient_amount', 'status', 'claim_date', 'notes',
    ];

    protected $casts = ['claim_date' => 'date'];

    public static $statusLabels = [
        'pending' => 'قيد المراجعة',
        'approved' => 'موافق عليها',
        'rejected' => 'مرفوضة',
        'paid' => 'مدفوعة',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function insurance()
    {
        return $this->belongsTo(Insurance::class);
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }
}
