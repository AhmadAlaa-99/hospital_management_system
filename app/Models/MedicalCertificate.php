<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class MedicalCertificate extends Model
{
    protected $fillable = [
        'patient_id', 'doctor_id', 'diagnostic_id', 'type', 'title', 'content',
        'reference_number', 'valid_from', 'valid_until', 'days_off', 'issued_at',
    ];

    protected $casts = [
        'valid_from' => 'date',
        'valid_until' => 'date',
        'issued_at' => 'datetime',
    ];

    public static $typeLabels = [
        'sick_leave' => 'إجازة مرضية',
        'fitness' => 'شهادة لياقة',
        'medical_report' => 'تقرير طبي',
    ];

    public static function generateReference(): string
    {
        do {
            $ref = 'MC-' . strtoupper(Str::random(8));
        } while (static::where('reference_number', $ref)->exists());

        return $ref;
    }

    public function patient() { return $this->belongsTo(Patient::class); }
    public function doctor() { return $this->belongsTo(Doctor::class); }
    public function diagnostic() { return $this->belongsTo(Diagnostic::class); }
}
