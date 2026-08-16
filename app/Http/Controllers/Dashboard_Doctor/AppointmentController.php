<?php

namespace App\Http\Controllers\Dashboard_Doctor;

use App\Http\Controllers\Controller;
use App\Models\Appointment;

class AppointmentController extends Controller
{
    public function index()
    {
        $appointments = Appointment::with('section')
            ->where('doctor_id', auth()->id())
            ->whereIn('type', ['غير مؤكد', 'مؤكد'])
            ->latest()
            ->get();

        return view('Dashboard.doctor.appointments.index', compact('appointments'));
    }

    public function finished()
    {
        $appointments = Appointment::with('section')
            ->where('doctor_id', auth()->id())
            ->whereIn('type', ['منتهي', 'مرفوض'])
            ->latest()
            ->get();

        return view('Dashboard.doctor.appointments.finished', compact('appointments'));
    }
}
