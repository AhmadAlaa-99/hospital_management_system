<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShamCashPayment extends Model
{
    protected $fillable = [
        'invoice_id', 'patient_id', 'amount', 'status', 'receipt_path',
        'transaction_reference', 'patient_notes', 'admin_notes', 'reviewed_by', 'reviewed_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'reviewed_at' => 'datetime',
    ];

    public static $statusLabels = [
        'pending_review' => 'جاري مراجعة الدفع',
        'approved' => 'مدفوعة',
        'rejected' => 'مرفوضة',
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }
}
