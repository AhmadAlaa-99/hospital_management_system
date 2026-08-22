<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Diagnostic extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'review_date',
        'review_reminder_sent',
        'diagnosis',
        'medicine',
        'invoice_id',
        'patient_id',
        'doctor_id',
    ];

    protected $casts = [
        'review_date' => 'datetime',
        'review_reminder_sent' => 'boolean',
    ];

    public function Doctor()
    {
        return $this->belongsTo(Doctor::class, 'doctor_id');
    }

    public function doctor()
    {
        return $this->belongsTo(Doctor::class, 'doctor_id');
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class, 'patient_id');
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'invoice_id');
    }

    public function prescriptions()
    {
        return $this->hasMany(Prescription::class, 'diagnostic_id');
    }
}
