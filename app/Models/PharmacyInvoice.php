<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class PharmacyInvoice extends Model
{
    protected $fillable = [
        'invoice_number', 'patient_id', 'diagnostic_id', 'doctor_id',
        'subtotal', 'total_amount', 'dispensed_by', 'dispensed_by_type', 'notes', 'issued_at',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'issued_at' => 'datetime',
    ];

    public static function generateNumber(): string
    {
        do {
            $number = 'PH-' . now()->format('ymd') . '-' . strtoupper(Str::random(4));
        } while (static::where('invoice_number', $number)->exists());

        return $number;
    }

    public function patient() { return $this->belongsTo(Patient::class); }
    public function diagnostic() { return $this->belongsTo(Diagnostic::class); }
    public function doctor() { return $this->belongsTo(Doctor::class); }
    public function items() { return $this->hasMany(PharmacyDispensing::class); }
}
