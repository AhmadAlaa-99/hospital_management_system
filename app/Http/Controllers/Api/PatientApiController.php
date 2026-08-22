<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\AmbulanceRequest;
use App\Models\Diagnostic;
use App\Models\FollowUpPlan;
use App\Models\Laboratorie;
use App\Models\PatientApiToken;
use App\Models\Prescription;
use App\Models\QueueTicket;
use App\Models\Ray;
use Illuminate\Http\Request;

class PatientApiController extends Controller
{
    protected function patientId(Request $request): int
    {
        return (int) $request->attributes->get('patient_id');
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $patient = \App\Models\Patient::where('email', $data['email'])->first();

        if (!$patient || !\Hash::check($data['password'], $patient->getAuthPassword())) {
            return response()->json(['message' => 'بيانات الدخول غير صحيحة'], 401);
        }

        $token = PatientApiToken::generateForPatient($patient->id);

        return response()->json([
            'token' => $token->token,
            'expires_at' => $token->expires_at,
            'patient' => [
                'id' => $patient->id,
                'name' => $patient->name,
                'email' => $patient->email,
            ],
        ]);
    }

    public function appointments(Request $request)
    {
        $patient = \App\Models\Patient::find($this->patientId($request));

        $appointments = Appointment::with(['doctor', 'section'])
            ->where('email', $patient->email)
            ->latest()
            ->get()
            ->map(fn ($a) => [
                'id' => $a->id,
                'doctor' => optional($a->doctor)->name,
                'section' => optional($a->section)->name,
                'type' => $a->type,
                'consultation_type' => $a->consultation_type ?? 'in_person',
                'meeting_url' => $a->meeting_url,
                'preferred_date' => $a->preferred_date,
                'preferred_time' => $a->preferred_time,
                'appointment' => $a->appointment,
            ]);

        return response()->json(['appointments' => $appointments]);
    }

    public function labResults(Request $request)
    {
        $patientId = $this->patientId($request);

        $results = Laboratorie::where('patient_id', $patientId)
            ->latest()
            ->get(['id', 'description', 'created_at']);

        return response()->json(['lab_results' => $results]);
    }

    public function rayResults(Request $request)
    {
        $patientId = $this->patientId($request);

        $results = Ray::where('patient_id', $patientId)
            ->latest()
            ->get(['id', 'description', 'created_at']);

        return response()->json(['ray_results' => $results]);
    }

    public function prescriptions(Request $request)
    {
        $patientId = $this->patientId($request);

        $prescriptions = Prescription::whereHas('diagnostic', fn ($q) => $q->where('patient_id', $patientId))
            ->with('diagnostic')
            ->latest()
            ->get();

        return response()->json(['prescriptions' => $prescriptions]);
    }

    public function followUps(Request $request)
    {
        $plans = FollowUpPlan::with(['doctor', 'section'])
            ->where('patient_id', $this->patientId($request))
            ->orderBy('follow_up_date')
            ->get();

        return response()->json(['follow_ups' => $plans]);
    }

    public function queuePosition(Request $request, string $ticketNumber)
    {
        $ticket = QueueTicket::where('ticket_number', $ticketNumber)->first();

        if (!$ticket) {
            return response()->json(['message' => 'التذكرة غير موجودة'], 404);
        }

        $ahead = QueueTicket::where('section_id', $ticket->section_id)
            ->where('doctor_id', $ticket->doctor_id)
            ->where('status', 'waiting')
            ->where('id', '<', $ticket->id)
            ->count();

        return response()->json([
            'ticket_number' => $ticket->ticket_number,
            'status' => $ticket->status,
            'position_ahead' => $ahead,
        ]);
    }

    public function requestAmbulance(Request $request)
    {
        $data = $request->validate([
            'patient_name' => 'required|string|min:3',
            'phone' => 'required|string|min:8',
            'address' => 'required|string',
            'notes' => 'nullable|string',
            'triage_level' => 'nullable|in:critical,urgent,normal',
        ]);

        $patientId = $this->patientId($request);

        $ambulanceRequest = AmbulanceRequest::create(array_merge($data, [
            'patient_id' => $patientId,
            'status' => 'pending',
            'triage_level' => $data['triage_level'] ?? 'normal',
            'requested_at' => now(),
        ]));

        \App\Services\AmbulanceWorkflowService::initialTimeline($ambulanceRequest);
        \App\Services\NotificationService::notifyAdmin('طلب إسعاف API من مريض #' . $patientId);

        return response()->json(['success' => true, 'request_id' => $ambulanceRequest->id]);
    }

    public function ambulanceStatus(Request $request, int $id)
    {
        $ambulanceRequest = AmbulanceRequest::with('timelines')
            ->where('patient_id', $this->patientId($request))
            ->findOrFail($id);

        return response()->json([
            'status' => $ambulanceRequest->status,
            'triage_level' => $ambulanceRequest->triage_level,
            'timelines' => $ambulanceRequest->timelines,
        ]);
    }
}
