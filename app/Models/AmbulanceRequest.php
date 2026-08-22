<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AmbulanceRequest extends Model
{
    protected $fillable = [
        'patient_name', 'phone', 'address', 'notes', 'ambulance_id', 'status', 'requested_at',
        'triage_level', 'section_id', 'doctor_id', 'appointment_id', 'transferred_to_clinic',
        'transfer_notes', 'patient_id',
    ];

    protected $casts = [
        'requested_at' => 'datetime',
        'transferred_to_clinic' => 'boolean',
    ];

    public static $statusLabels = [
        'pending' => 'قيد الانتظار',
        'dispatched' => 'تم الإرسال',
        'en_route' => 'في الطريق',
        'arrived' => 'وصلت السيارة',
        'transported' => 'تم نقل المريض',
        'completed' => 'مكتمل',
        'cancelled' => 'ملغى',
    ];

    public static $triageLabels = [
        'critical' => 'حرج',
        'urgent' => 'عاجل',
        'normal' => 'عادي',
    ];

    public static $triageColors = [
        'critical' => 'danger',
        'urgent' => 'warning',
        'normal' => 'success',
    ];

    public function ambulance()
    {
        return $this->belongsTo(Ambulance::class);
    }

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

    public function timelines()
    {
        return $this->hasMany(AmbulanceRequestTimeline::class)->orderBy('recorded_at');
    }

    public static function busyAmbulanceIds(): array
    {
        return static::query()
            ->whereIn('status', ['dispatched', 'en_route', 'arrived', 'transported'])
            ->whereNotNull('ambulance_id')
            ->pluck('ambulance_id')
            ->all();
    }
}
