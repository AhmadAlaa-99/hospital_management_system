<?php

namespace App\Http\Controllers\Dashboard_Doctor;

use App\Http\Controllers\Controller;
use App\Models\Doctor;
use App\Models\Invoice;
use App\Models\Patient;
use App\Models\Referral;
use App\Helpers\FriendlyError;
use App\Services\AuditLogService;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReferralController extends Controller
{
    public function index()
    {
        $doctorId = Auth::guard('doctor')->id();

        $sent = Referral::with(['patient', 'toDoctor', 'toSection'])
            ->where('from_doctor_id', $doctorId)
            ->latest()
            ->paginate(15, ['*'], 'sent');

        $received = Referral::with(['patient', 'fromDoctor', 'fromSection'])
            ->where('to_doctor_id', $doctorId)
            ->latest()
            ->paginate(15, ['*'], 'received');

        return view('Dashboard.doctor.referrals.index', compact('sent', 'received'));
    }

    public function create()
    {
        $doctorId = Auth::guard('doctor')->id();
        $doctors = Doctor::with('section')
            ->where('id', '!=', $doctorId)
            ->where('status', 1)
            ->get();

        $patientIds = Invoice::where('doctor_id', $doctorId)->distinct()->pluck('patient_id');
        $patients = Patient::whereIn('id', $patientIds)->orderBy('name')->get();

        if ($patients->isEmpty()) {
            $patients = Patient::orderBy('name')->limit(300)->get();
        }

        return view('Dashboard.doctor.referrals.create', compact('doctors', 'patients'));
    }

    public function store(Request $request)
    {
        $doctor = Auth::guard('doctor')->user();

        $data = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'to_doctor_id' => 'required|exists:doctors,id',
            'diagnostic_id' => 'nullable|exists:diagnostics,id',
            'reason' => 'required|string|min:3|max:1000',
            'notes' => 'nullable|string|max:500',
        ], [
            'patient_id.required' => 'يرجى اختيار المريض.',
            'patient_id.exists' => 'المريض غير موجود.',
            'to_doctor_id.required' => 'يرجى اختيار الطبيب المحوّل إليه.',
            'to_doctor_id.exists' => 'الطبيب المختار غير موجود.',
            'reason.required' => 'يرجى كتابة سبب التحويل.',
            'reason.min' => 'سبب التحويل قصير جداً (3 أحرف على الأقل).',
        ]);

        if ((int) $data['to_doctor_id'] === (int) $doctor->id) {
            return back()->withInput()->withErrors(['error' => 'لا يمكن التحويل لنفس الطبيب.']);
        }

        if (!Doctor::where('id', $data['to_doctor_id'])->where('status', 1)->exists()) {
            return back()->withInput()->withErrors(['error' => 'الطبيب المحوّل إليه غير متاح.']);
        }

        $toDoctor = Doctor::findOrFail($data['to_doctor_id']);

        try {
            $referral = Referral::create([
                'patient_id' => $data['patient_id'],
                'from_doctor_id' => $doctor->id,
                'to_doctor_id' => $data['to_doctor_id'],
                'from_section_id' => $doctor->section_id,
                'to_section_id' => $toDoctor->section_id,
                'diagnostic_id' => $data['diagnostic_id'] ?? null,
                'reason' => $data['reason'],
                'notes' => $data['notes'] ?? null,
                'status' => 'pending',
            ]);

            try {
                AuditLogService::log('referral_created', $referral, null, $referral->toArray());
                NotificationService::notifyDoctor(
                    (int) $toDoctor->id,
                    'تحويل جديد من د. ' . ($doctor->name ?? '') . ' — يرجى المراجعة'
                );
                NotificationService::notifyAdmin(
                    'تحويل بين تخصصات: مريض #' . $data['patient_id'],
                    route('admin.referrals.index')
                );
            } catch (\Throwable $e) {
                report($e);
            }

            session()->flash('add');
            return redirect()->route('doctor.referrals.index');
        } catch (\Throwable $e) {
            report($e);

            return back()->withInput()->withErrors([
                'error' => FriendlyError::message($e->getMessage()),
            ]);
        }
    }

    public function accept(Referral $referral)
    {
        $this->authorizeDoctor($referral);

        if ($referral->status !== 'pending') {
            return back()->withErrors(['error' => 'لا يمكن قبول هذا التحويل.']);
        }

        $referral->update(['status' => 'accepted', 'accepted_at' => now()]);
        AuditLogService::log('referral_accepted', $referral);

        NotificationService::notifyDoctor(
            (int) $referral->from_doctor_id,
            'تم قبول التحويل للمريض #' . $referral->patient_id
        );

        session()->flash('edit');
        return back();
    }

    public function complete(Referral $referral)
    {
        $this->authorizeDoctor($referral);

        $referral->update(['status' => 'completed', 'completed_at' => now()]);
        AuditLogService::log('referral_completed', $referral);

        session()->flash('edit');
        return back();
    }

    public function reject(Referral $referral)
    {
        $this->authorizeDoctor($referral);

        $referral->update(['status' => 'rejected']);
        AuditLogService::log('referral_rejected', $referral);

        NotificationService::notifyDoctor(
            (int) $referral->from_doctor_id,
            'تم رفض التحويل للمريض #' . $referral->patient_id
        );

        session()->flash('delete');
        return back();
    }

    protected function authorizeDoctor(Referral $referral): void
    {
        if ($referral->to_doctor_id !== Auth::guard('doctor')->id()) {
            abort(403);
        }
    }
}
