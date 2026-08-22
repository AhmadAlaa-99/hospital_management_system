<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Ambulance;
use App\Models\AmbulanceRequest;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Section;
use App\Services\AmbulanceWorkflowService;
use App\Services\AuditLogService;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class AmbulanceRequestController extends Controller
{
    public function index()
    {
        $requests = AmbulanceRequest::with(['ambulance', 'section', 'doctor', 'timelines'])
            ->orderByRaw("FIELD(triage_level, 'critical', 'urgent', 'normal')")
            ->latest()
            ->paginate(20);

        $availableAmbulances = Ambulance::availableForDispatch()->get();
        $sections = Section::with('translations')->get();
        $doctors = Doctor::where('status', 1)->get();

        return view('Dashboard.AmbulanceRequests.index', compact('requests', 'availableAmbulances', 'sections', 'doctors'));
    }

    public function assignAmbulance(Request $request, AmbulanceRequest $ambulanceRequest)
    {
        $request->validate(['ambulance_id' => 'required|exists:ambulances,id']);

        $isAvailable = Ambulance::availableForDispatch()
            ->where('id', $request->ambulance_id)
            ->exists();

        if (!$isAvailable) {
            return back()->withErrors(['error' => 'السيارة المختارة غير متاحة.']);
        }

        if ($ambulanceRequest->status !== 'pending') {
            return back()->withErrors(['error' => 'لا يمكن إسناد سيارة لهذا الطلب في حالته الحالية.']);
        }

        $ambulanceRequest->update(['ambulance_id' => $request->ambulance_id]);
        Ambulance::where('id', $request->ambulance_id)->update(['is_available' => 2]);

        AmbulanceWorkflowService::recordTimeline($ambulanceRequest, 'dispatched', 'تم إرسال سيارة الإسعاف');

        AuditLogService::log('ambulance_dispatched', $ambulanceRequest);
        NotificationService::notifyAdmin('تم إرسال إسعاف للمريض: ' . $ambulanceRequest->patient_name, route('ambulance-requests.index'));

        session()->flash('edit');
        return back();
    }

    public function advanceStatus(Request $request, AmbulanceRequest $ambulanceRequest)
    {
        $request->validate([
            'status' => 'required|in:en_route,arrived,transported,completed',
            'notes' => 'nullable|string|max:500',
        ]);

        $nextStatus = $request->status;

        if ($nextStatus === 'completed' && $ambulanceRequest->ambulance_id) {
            Ambulance::where('id', $ambulanceRequest->ambulance_id)->update(['is_available' => 1]);
        }

        AmbulanceWorkflowService::recordTimeline($ambulanceRequest, $nextStatus, $request->notes);
        AuditLogService::log('ambulance_status_' . $nextStatus, $ambulanceRequest);

        session()->flash('edit');
        return back();
    }

    public function transferToClinic(Request $request, AmbulanceRequest $ambulanceRequest)
    {
        $data = $request->validate([
            'section_id' => 'required|exists:sections,id',
            'doctor_id' => 'nullable|exists:doctors,id',
            'transfer_notes' => 'nullable|string|max:500',
            'create_appointment' => 'nullable|boolean',
        ]);

        $patient = null;
        if ($ambulanceRequest->patient_id) {
            $patient = Patient::find($ambulanceRequest->patient_id);
        }

        $appointment = null;
        if ($request->boolean('create_appointment')) {
            $doctorId = $data['doctor_id'] ?? Doctor::where('section_id', $data['section_id'])->where('status', 1)->value('id');

            if ($doctorId) {
                $appointment = Appointment::create([
                    'doctor_id' => $doctorId,
                    'section_id' => $data['section_id'],
                    'name' => $ambulanceRequest->patient_name,
                    'email' => $patient->email ?? ('ambulance' . $ambulanceRequest->id . '@temp.local'),
                    'phone' => $ambulanceRequest->phone,
                    'notes' => 'تحويل من الإسعاف: ' . ($data['transfer_notes'] ?? ''),
                    'preferred_date' => now()->toDateString(),
                    'preferred_time' => now()->format('H:i:s'),
                    'type' => 'غير مؤكد',
                    'is_emergency' => true,
                    'consultation_type' => 'in_person',
                ]);

                if ($doctorId) {
                    NotificationService::notifyDoctor((int) $doctorId, 'مريض إسعاف محوّل للعيادة: ' . $ambulanceRequest->patient_name);
                }
            }
        }

        $ambulanceRequest->update([
            'section_id' => $data['section_id'],
            'doctor_id' => $data['doctor_id'] ?? null,
            'appointment_id' => $appointment ? $appointment->id : null,
            'transferred_to_clinic' => true,
            'transfer_notes' => $data['transfer_notes'] ?? null,
        ]);

        AmbulanceWorkflowService::recordTimeline($ambulanceRequest, $ambulanceRequest->status, 'تم تحويل المريض لعيادة التخصص');
        AuditLogService::log('ambulance_clinic_transfer', $ambulanceRequest);

        session()->flash('add');
        return back();
    }

    public function complete(AmbulanceRequest $ambulanceRequest)
    {
        if ($ambulanceRequest->ambulance_id) {
            Ambulance::where('id', $ambulanceRequest->ambulance_id)->update(['is_available' => 1]);
        }

        AmbulanceWorkflowService::recordTimeline($ambulanceRequest, 'completed', 'اكتمل طلب الإسعاف');

        session()->flash('edit');
        return back();
    }

    public function cancel(AmbulanceRequest $ambulanceRequest)
    {
        if ($ambulanceRequest->ambulance_id && in_array($ambulanceRequest->status, ['dispatched', 'en_route', 'arrived', 'transported'])) {
            Ambulance::where('id', $ambulanceRequest->ambulance_id)->update(['is_available' => 1]);
        }

        AmbulanceWorkflowService::recordTimeline($ambulanceRequest, 'cancelled', 'تم إلغاء الطلب');
        AuditLogService::log('ambulance_cancelled', $ambulanceRequest);

        session()->flash('delete');
        return back();
    }
}
