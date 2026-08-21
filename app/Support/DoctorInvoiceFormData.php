<?php

namespace App\Support;

use App\Models\Group;
use App\Models\Patient;
use App\Models\QueueTicket;
use App\Models\Service;

class DoctorInvoiceFormData
{
    public static function forDoctor($doctor): array
    {
        $todayPatientIds = QueueTicket::today()
            ->where('doctor_id', $doctor->id)
            ->whereNotNull('patient_id')
            ->pluck('patient_id')
            ->unique()
            ->filter();

        $patients = Patient::when($todayPatientIds->isNotEmpty(), function ($q) use ($todayPatientIds) {
            $q->whereIn('id', $todayPatientIds);
        })->orderBy('id')->get();

        if ($patients->isEmpty()) {
            $patients = Patient::orderBy('id')->limit(200)->get();
        }

        return [
            'doctor' => $doctor,
            'patients' => $patients,
            'services' => Service::orderBy('id')->get(),
            'groups' => Group::orderBy('id')->get(),
            'todayTickets' => QueueTicket::today()
                ->where('doctor_id', $doctor->id)
                ->whereIn('status', ['called', 'serving', 'completed'])
                ->whereNotNull('patient_id')
                ->orderBy('daily_sequence')
                ->get(),
        ];
    }
}
