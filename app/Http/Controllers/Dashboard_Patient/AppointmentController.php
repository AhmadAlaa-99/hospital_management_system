<?php

namespace App\Http\Controllers\Dashboard_Patient;

use App\Http\Controllers\Controller;
use App\Models\Appointment;

class AppointmentController extends Controller
{
    public function index()
    {
        $patient = auth()->user();

        $appointments = Appointment::with(['doctor', 'section'])
            ->where('email', $patient->email)
            ->latest()
            ->get();

        return view('Dashboard.dashboard_patient.appointments.index', compact('appointments'));
    }
}
