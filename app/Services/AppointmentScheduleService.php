<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\DoctorSchedule;
use Carbon\Carbon;

class AppointmentScheduleService
{
    public function isWithinSchedule(int $doctorId, Carbon $datetime): bool
    {
        $dayOfWeek = (int) $datetime->dayOfWeek;
        $time = $datetime->format('H:i:s');

        $schedule = DoctorSchedule::where('doctor_id', $doctorId)
            ->where('day_of_week', $dayOfWeek)
            ->first();

        if ($schedule) {
            return $time >= $schedule->start_time && $time <= $schedule->end_time;
        }

        // بدون جدول لهذا اليوم: السماح بأيام العمل الافتراضية 09:00–17:00
        if (!in_array($dayOfWeek, [0, 1, 2, 3, 4, 6], true)) {
            return false;
        }

        return $time >= '09:00:00' && $time <= '17:00:00';
    }

    public function hasConflict(int $doctorId, Carbon $datetime, ?int $excludeAppointmentId = null): bool
    {
        $slotStart = $datetime->copy()->subMinutes(29);
        $slotEnd = $datetime->copy()->addMinutes(29);

        $query = Appointment::where('doctor_id', $doctorId)
            ->where('type', 'مؤكد')
            ->whereNotNull('appointment')
            ->whereBetween('appointment', [$slotStart, $slotEnd]);

        if ($excludeAppointmentId) {
            $query->where('id', '!=', $excludeAppointmentId);
        }

        return $query->exists();
    }

    public function getAvailableSlots(int $doctorId, string $date): array
    {
        return $this->resolveAvailableSlots($doctorId, $date)['slots'];
    }

    public function resolveAvailableSlots(int $doctorId, string $date): array
    {
        $carbonDate = Carbon::parse($date)->startOfDay();
        $dayOfWeek = (int) $carbonDate->dayOfWeek;

        if ($carbonDate->lt(Carbon::today())) {
            return [
                'slots' => [],
                'message' => 'التاريخ المختار في الماضي — اختر تاريخاً من اليوم فصاعداً',
            ];
        }

        $schedule = DoctorSchedule::where('doctor_id', $doctorId)
            ->where('day_of_week', $dayOfWeek)
            ->first();

        // إن لم يكن للطبيب جدول لهذا اليوم: أوقات افتراضية أيام العمل (أحد–خميس + سبت)
        if (!$schedule) {
            if (!in_array($dayOfWeek, [0, 1, 2, 3, 4, 6], true)) {
                return [
                    'slots' => [],
                    'message' => 'يوم الجمعة عطلة — اختر يوماً آخر',
                ];
            }
            $duration = 30;
            $start = $carbonDate->copy()->setTime(9, 0);
            $end = $carbonDate->copy()->setTime(17, 0);
        } else {
            $duration = max(15, (int) $schedule->slot_duration);
            $start = Carbon::parse($date . ' ' . $schedule->start_time);
            $end = Carbon::parse($date . ' ' . $schedule->end_time);
        }

        $slots = [];
        $now = Carbon::now();

        while ($start->copy()->addMinutes($duration)->lte($end)) {
            if ($start->greaterThan($now) && !$this->hasConflict($doctorId, $start->copy())) {
                if ($this->countPendingAtSlot($doctorId, $start->copy()) < 3) {
                    $slots[] = $start->format('H:i');
                }
            }
            $start->addMinutes($duration);
        }

        if ($slots !== []) {
            return ['slots' => $slots, 'message' => null];
        }

        $message = $carbonDate->isToday()
            ? 'لا توجد أوقات متبقية اليوم — اختر تاريخاً لاحقاً'
            : 'جميع الأوقات محجوزة أو غير متاحة في هذا اليوم — جرّب تاريخاً آخر';

        return ['slots' => [], 'message' => $message];
    }

    public function validateSlot(int $doctorId, Carbon $datetime, ?int $excludeAppointmentId = null): ?string
    {
        if (!$this->isWithinSchedule($doctorId, $datetime)) {
            return 'الوقت المختار خارج ساعات عمل الطبيب';
        }

        if ($this->hasConflict($doctorId, $datetime, $excludeAppointmentId)) {
            return 'يوجد موعد آخر محجوز في هذا الوقت';
        }

        return null;
    }

    public function countPendingAtSlot(int $doctorId, Carbon $datetime): int
    {
        if (!$datetime->format('Y-m-d') || !$datetime->format('H:i')) {
            return 0;
        }

        return Appointment::where('doctor_id', $doctorId)
            ->where('type', 'غير مؤكد')
            ->whereDate('preferred_date', $datetime->toDateString())
            ->whereTime('preferred_time', $datetime->format('H:i:s'))
            ->count();
    }
}
