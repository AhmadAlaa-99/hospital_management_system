<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PharmacyDispensing extends Model
{
    protected $fillable = [
        'pharmacy_invoice_id', 'patient_id', 'diagnostic_id', 'prescription_id', 'medicine_id',
        'quantity_dispensed', 'unit_price', 'total_price', 'dispensed_by', 'dispensed_by_type', 'dispensed_at',
    ];

    protected $casts = [
        'dispensed_at' => 'datetime',
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
    ];

    public function patient() { return $this->belongsTo(Patient::class); }
    public function diagnostic() { return $this->belongsTo(Diagnostic::class); }
    public function prescription() { return $this->belongsTo(Prescription::class); }
    public function medicine() { return $this->belongsTo(Medicine::class); }
    public function pharmacyInvoice() { return $this->belongsTo(PharmacyInvoice::class); }
}
