<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DoctorRating extends Model
{
    public const HOMEPAGE_NONE = 'none';
    public const HOMEPAGE_PENDING = 'pending';
    public const HOMEPAGE_APPROVED = 'approved';
    public const HOMEPAGE_REJECTED = 'rejected';

    public static $homepageStatusLabels = [
        self::HOMEPAGE_NONE => '—',
        self::HOMEPAGE_PENDING => 'بانتظار الموافقة',
        self::HOMEPAGE_APPROVED => 'منشورة في الموقع',
        self::HOMEPAGE_REJECTED => 'مرفوضة',
    ];

    protected $fillable = [
        'doctor_id',
        'patient_id',
        'appointment_id',
        'rating',
        'comment',
        'share_on_homepage',
        'homepage_status',
    ];

    protected $casts = [
        'share_on_homepage' => 'boolean',
    ];

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }

    public function scopePublishedOnHomepage($query)
    {
        return $query
            ->where('homepage_status', self::HOMEPAGE_APPROVED)
            ->whereNotNull('comment')
            ->where('comment', '!=', '');
    }

    public function patientDisplayName(): string
    {
        $name = trim((string) optional($this->patient)->name);
        if ($name === '') {
            return 'مريض';
        }

        $parts = preg_split('/\s+/u', $name) ?: [];
        return $parts[0] ?? $name;
    }

    public function patientRoleLabel(): string
    {
        $section = optional(optional($this->doctor)->section)->name;
        if ($section) {
            return 'مريض — ' . $section;
        }

        $doctorName = optional($this->doctor)->name;
        if ($doctorName) {
            return 'مريض — ' . $doctorName;
        }

        return 'مريض';
    }

    public function homepageAvatar(int $index = 0): string
    {
        $avatars = [
            'WebSite/images/hms/authors/a2.jpg',
            'WebSite/images/hms/authors/a3.jpg',
            'WebSite/images/hms/authors/a4.jpg',
            'WebSite/images/hms/doctors/d2.jpg',
        ];

        return asset($avatars[$index % count($avatars)]);
    }
}
