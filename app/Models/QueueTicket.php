<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QueueTicket extends Model
{
    protected $fillable = [
        'ticket_number', 'daily_sequence', 'queue_date', 'section_id', 'doctor_id',
        'appointment_id', 'patient_id', 'patient_name', 'phone', 'status', 'priority',
        'estimated_wait_minutes', 'issued_at', 'called_at', 'serving_at', 'completed_at',
    ];

    protected $casts = [
        'queue_date' => 'date',
        'issued_at' => 'datetime',
        'called_at' => 'datetime',
        'serving_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public static $statusLabels = [
        'waiting' => 'بالانتظار',
        'called' => 'تم النداء',
        'serving' => 'عند الطبيب',
        'completed' => 'مكتمل',
        'no_show' => 'لم يحضر',
        'cancelled' => 'ملغى',
    ];

    public static $priorityLabels = [
        'normal' => 'عادي',
        'urgent' => 'عاجل',
        'elderly' => 'كبار سن',
    ];

    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('queue_date', today());
    }

    public function scopeActive($query)
    {
        return $query->whereIn('status', ['waiting', 'called', 'serving']);
    }
}
