<?php

namespace App\Http\Controllers\Dashboard_Doctor;

use App\Http\Controllers\Controller;
use App\Models\FollowUpPlan;
use App\Services\AuditLogService;
use App\Services\FollowUpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FollowUpPlanController extends Controller
{
    public function index()
    {
        $plans = FollowUpPlan::with(['patient', 'section'])
            ->where('doctor_id', Auth::guard('doctor')->id())
            ->orderBy('follow_up_date')
            ->paginate(20);

        return view('Dashboard.doctor.follow_ups.index', compact('plans'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'diagnostic_id' => 'nullable|exists:diagnostics,id',
            'follow_up_date' => 'required|date|after_or_equal:today',
            'notes' => 'nullable|string|max:500',
            'create_appointment' => 'nullable|boolean',
        ]);

        $doctor = Auth::guard('doctor')->user();

        $plan = FollowUpPlan::create([
            'patient_id' => $data['patient_id'],
            'doctor_id' => $doctor->id,
            'section_id' => $doctor->section_id,
            'diagnostic_id' => $data['diagnostic_id'] ?? null,
            'follow_up_date' => $data['follow_up_date'],
            'notes' => $data['notes'] ?? null,
            'status' => 'scheduled',
        ]);

        AuditLogService::log('follow_up_created', $plan);

        if ($request->boolean('create_appointment')) {
            FollowUpService::createAppointmentForPlan($plan);
        }

        session()->flash('add');
        return back();
    }

    public function complete(FollowUpPlan $followUp)
    {
        if ($followUp->doctor_id !== Auth::guard('doctor')->id()) {
            abort(403);
        }

        $followUp->update(['status' => 'completed']);
        AuditLogService::log('follow_up_completed', $followUp);

        session()->flash('edit');
        return back();
    }

    public function createAppointment(FollowUpPlan $followUp)
    {
        if ($followUp->doctor_id !== Auth::guard('doctor')->id()) {
            abort(403);
        }

        FollowUpService::createAppointmentForPlan($followUp);

        session()->flash('add');
        return back();
    }
}
