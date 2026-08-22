<?php

namespace App\Http\Controllers;

use App\Models\AmbulanceRequest;
use App\Services\AmbulanceWorkflowService;
use App\Services\AuditLogService;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class AmbulanceRequestPublicController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'patient_name' => 'required|string|min:3|max:100',
            'phone' => 'required|string|min:8|max:20',
            'address' => 'required|string|max:500',
            'notes' => 'nullable|string|max:500',
            'triage_level' => 'nullable|in:critical,urgent,normal',
        ], [
            'patient_name.required' => 'الاسم مطلوب',
            'phone.required' => 'رقم الهاتف مطلوب',
            'address.required' => 'العنوان مطلوب',
        ]);

        $ambulanceRequest = AmbulanceRequest::create(array_merge($data, [
            'status' => 'pending',
            'triage_level' => $data['triage_level'] ?? 'normal',
            'requested_at' => now(),
        ]));

        AmbulanceWorkflowService::initialTimeline($ambulanceRequest);
        AuditLogService::log('ambulance_request_created', $ambulanceRequest);

        $triageLabel = AmbulanceRequest::$triageLabels[$ambulanceRequest->triage_level] ?? '';
        NotificationService::notifyAdmin(
            'طلب إسعاف جديد (' . $triageLabel . ') من: ' . $data['patient_name'],
            route('ambulance-requests.index')
        );

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'تم إرسال إشعار مستعجل للإدارة، وسيتم التواصل معك على رقم الهاتف المسجل.',
            ]);
        }

        return back()->with('ambulance_success', true);
    }
}
