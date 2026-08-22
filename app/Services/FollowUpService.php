<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\Diagnostic;
use App\Models\Doctor;
use App\Models\FollowUpPlan;
use App\Models\Patient;
use Carbon\Carbon;

class FollowUpService
{
    public static function createFromDiagnostic(Diagnostic $diagnostic, string $followUpDate, ?string $notes = null): FollowUpPlan
    {
        $doctor = Doctor::find($diagnostic->doctor_id);

        $plan = FollowUpPlan::create([
            'patient_id' => $diagnostic->patient_id,
            'doctor_id' => $diagnostic->doctor_id,
            'section_id' => $doctor ? $doctor->section_id : null,
            'diagnostic_id' => $diagnostic->id,
            'follow_up_date' => $followUpDate,
            'notes' => $notes,
            'status' => 'scheduled',
        ]);

        AuditLogService::log('follow_up_created', $plan, null, $plan->toArray());

        NotificationService::notifyPatient(
            (int) $diagnostic->patient_id,
            'تم جدولة متابعة طبية بتاريخ ' . Carbon::parse($followUpDate)->format('Y-m-d')
        );

        return $plan;
    }

    public static function createAppointmentForPlan(FollowUpPlan $plan): ?Appointment
    {
        $patient = Patient::find($plan->patient_id);
        $doctor = Doctor::find($plan->doctor_id);

        if (!$patient || !$doctor) {
            return null;
        }

        $appointment = Appointment::create([
            'doctor_id' => $plan->doctor_id,
            'section_id' => $plan->section_id ?? $doctor->section_id,
            'name' => (string) ($patient->name ?: 'مريض'),
            'email' => $patient->email,
            'phone' => (string) ($patient->Phone ?? ''),
            'notes' => 'موعد متابعة: ' . ($plan->notes ?? ''),
            'preferred_date' => $plan->follow_up_date,
            'preferred_time' => '09:00:00',
            'type' => 'غير مؤكد',
            'consultation_type' => 'in_person',
        ]);

        $plan->update(['appointment_id' => $appointment->id]);

        NotificationService::notifyDoctor(
            (int) $plan->doctor_id,
            'موعد متابعة جديد للمريض: ' . ($patient->name ?? '')
        );

        return $appointment;
    }
}
