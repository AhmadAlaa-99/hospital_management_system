<?php

namespace App\Services;

use App\Models\Admin;
use App\Models\Notification;
use App\Models\Patient;

class NotificationService
{
    public static function send(int $userId, string $message, string $userType = 'doctor', ?string $actionUrl = null): Notification
    {
        $notification = new Notification();
        $notification->user_id = $userId;
        $notification->user_type = $userType;
        $notification->message = $message;
        $notification->action_url = $actionUrl;
        $notification->reader_status = 0;
        $notification->save();

        return $notification;
    }

    public static function notifyDoctor(int $doctorId, string $message): Notification
    {
        return self::send($doctorId, $message, 'doctor');
    }

    public static function notifyPatient(int $patientId, string $message): Notification
    {
        return self::send($patientId, $message, 'patient');
    }

    public static function notifyPatientByEmail(?string $email, string $message): ?Notification
    {
        if (!$email) {
            return null;
        }

        $patient = Patient::where('email', $email)->first();
        if (!$patient) {
            return null;
        }

        return self::notifyPatient($patient->id, $message);
    }

    public static function notifyAdmin(string $message, ?string $actionUrl = null): void
    {
        Admin::query()->each(function (Admin $admin) use ($message, $actionUrl) {
            self::send($admin->id, $message, 'admin', $actionUrl);
        });
    }

    public static function currentUserType(): string
    {
        if (auth('admin')->check()) {
            return 'admin';
        }
        if (auth('doctor')->check()) {
            return 'doctor';
        }
        if (auth('patient')->check()) {
            return 'patient';
        }
        if (auth('ray_employee')->check()) {
            return 'ray_employee';
        }
        if (auth('laboratorie_employee')->check()) {
            return 'laboratorie_employee';
        }

        return 'user';
    }

    public static function unreadForAuth()
    {
        return Notification::where('user_id', auth()->id())
            ->where(function ($q) {
                $q->where('user_type', self::currentUserType())
                    ->orWhereNull('user_type');
            })
            ->where('reader_status', 0)
            ->latest();
    }
}
