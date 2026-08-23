<?php

namespace App\Http\Controllers\Dashboard_Doctor;

use App\Helpers\FriendlyError;
use App\Http\Controllers\Controller;
use App\Models\MedicalCertificate;
use App\Models\Patient;
use App\Services\AuditLogService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class MedicalCertificateController extends Controller
{
    public function index()
    {
        $certificates = MedicalCertificate::with('patient')
            ->where('doctor_id', Auth::guard('doctor')->id())
            ->latest()
            ->paginate(20);

        return view('Dashboard.doctor.certificates.index', compact('certificates'));
    }

    public function create(Request $request)
    {
        $patientId = $request->get('patient_id');
        $patient = $patientId ? Patient::find($patientId) : null;

        return view('Dashboard.doctor.certificates.create', compact('patient'));
    }

    public function store(Request $request)
    {
        if (!Schema::hasTable('medical_certificates')) {
            return back()->withInput()->withErrors([
                'error' => 'جدول الشهادات الطبية غير موجود — نفّذ php artisan migrate --force على السيرفر.',
            ]);
        }

        $doctor = Auth::guard('doctor')->user();

        $data = $request->validate([
            'patient_id' => 'required|integer|exists:patients,id',
            'diagnostic_id' => 'nullable|integer|exists:diagnostics,id',
            'type' => 'required|in:sick_leave,fitness,medical_report',
            'title' => 'required|string|max:200',
            'content' => 'required|string|min:10',
            'valid_from' => 'nullable|date',
            'valid_until' => 'nullable|date',
            'days_off' => 'nullable|integer|min:1|max:365',
        ], [
            'patient_id.required' => 'يرجى إدخال رقم المريض.',
            'patient_id.exists' => 'رقم المريض غير موجود في النظام.',
            'type.required' => 'يرجى اختيار نوع الشهادة.',
            'title.required' => 'يرجى إدخال عنوان الشهادة.',
            'content.required' => 'يرجى إدخال محتوى الشهادة.',
            'content.min' => 'محتوى الشهادة قصير جداً (10 أحرف على الأقل).',
            'days_off.min' => 'أيام الإجازة يجب أن تكون يوماً واحداً على الأقل.',
        ]);

        if (!empty($data['valid_from']) && !empty($data['valid_until'])
            && $data['valid_until'] < $data['valid_from']) {
            return back()->withInput()->withErrors([
                'valid_until' => 'تاريخ «إلى» يجب أن يكون بعد تاريخ «من».',
            ]);
        }

        if ($data['type'] !== 'sick_leave') {
            $data['days_off'] = null;
        } elseif (empty($data['days_off'])) {
            return back()->withInput()->withErrors([
                'days_off' => 'يرجى إدخال عدد أيام الإجازة للإجازة المرضية.',
            ]);
        }

        try {
            $certificate = MedicalCertificate::create([
                'patient_id' => $data['patient_id'],
                'doctor_id' => $doctor->id,
                'diagnostic_id' => $data['diagnostic_id'] ?? null,
                'type' => $data['type'],
                'title' => $data['title'],
                'content' => $data['content'],
                'valid_from' => $data['valid_from'] ?? null,
                'valid_until' => $data['valid_until'] ?? null,
                'days_off' => $data['days_off'] ?? null,
                'reference_number' => MedicalCertificate::generateReference(),
                'issued_at' => now(),
            ]);

            if (Schema::hasTable('activity_logs')) {
                AuditLogService::log('certificate_issued', $certificate);
            }

            return $this->pdfDownload($certificate);
        } catch (\Throwable $e) {
            Log::error('Medical certificate store failed', [
                'doctor_id' => $doctor->id ?? null,
                'patient_id' => $data['patient_id'] ?? null,
                'message' => $e->getMessage(),
            ]);

            return back()->withInput()->withErrors([
                'error' => FriendlyError::message($e->getMessage()),
            ]);
        }
    }

    public function pdf(MedicalCertificate $certificate)
    {
        if ($certificate->doctor_id !== Auth::guard('doctor')->id()) {
            abort(403);
        }

        return $this->pdfDownload($certificate);
    }

    protected function pdfDownload(MedicalCertificate $certificate)
    {
        $certificate->load(['patient', 'doctor']);

        $pdf = Pdf::loadView('pdf.medical-certificate', compact('certificate'))->setPaper('a4');

        return $pdf->download('certificate-' . $certificate->reference_number . '.pdf');
    }
}
