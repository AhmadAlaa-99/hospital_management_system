<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FollowUpPlan extends Model
{
    protected $fillable = [
        'patient_id', 'doctor_id', 'section_id', 'diagnostic_id', 'follow_up_date',
        'notes', 'status', 'appointment_id', 'reminder_sent',
    ];

    protected $casts = [
        'follow_up_date' => 'date',
        'reminder_sent' => 'boolean',
    ];

    public static $statusLabels = [
        'scheduled' => 'مجدول',
        'completed' => 'مكتمل',
        'missed' => 'فائت',
        'cancelled' => 'ملغى',
    ];

    public function patient() { return $this->belongsTo(Patient::class); }
    public function doctor() { return $this->belongsTo(Doctor::class); }
    public function section() { return $this->belongsTo(Section::class); }
    public function diagnostic() { return $this->belongsTo(Diagnostic::class); }
    public function appointment() { return $this->belongsTo(Appointment::class); }
}
