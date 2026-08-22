<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PatientPackageUsage extends Model
{
    protected $fillable = [
        'patient_id', 'group_id', 'service_id', 'invoice_id',
        'quantity_allowed', 'quantity_used', 'purchased_at', 'expires_at',
    ];

    protected $casts = [
        'purchased_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function remainingQuantity(): int
    {
        return max(0, $this->quantity_allowed - $this->quantity_used);
    }

    public function patient() { return $this->belongsTo(Patient::class); }
    public function group() { return $this->belongsTo(Group::class); }
    public function service() { return $this->belongsTo(Service::class); }
    public function invoice() { return $this->belongsTo(Invoice::class); }
}
