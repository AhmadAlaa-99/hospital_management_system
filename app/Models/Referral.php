<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Referral extends Model
{
    protected $fillable = [
        'patient_id', 'from_doctor_id', 'to_doctor_id', 'from_section_id', 'to_section_id',
        'diagnostic_id', 'reason', 'notes', 'status', 'accepted_at', 'completed_at',
    ];

    protected $casts = [
        'accepted_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public static $statusLabels = [
        'pending' => 'قيد الانتظار',
        'accepted' => 'مقبول',
        'completed' => 'مكتمل',
        'rejected' => 'مرفوض',
    ];

    public function patient() { return $this->belongsTo(Patient::class); }
    public function fromDoctor() { return $this->belongsTo(Doctor::class, 'from_doctor_id'); }
    public function toDoctor() { return $this->belongsTo(Doctor::class, 'to_doctor_id'); }
    public function fromSection() { return $this->belongsTo(Section::class, 'from_section_id'); }
    public function toSection() { return $this->belongsTo(Section::class, 'to_section_id'); }
    public function diagnostic() { return $this->belongsTo(Diagnostic::class); }
}
