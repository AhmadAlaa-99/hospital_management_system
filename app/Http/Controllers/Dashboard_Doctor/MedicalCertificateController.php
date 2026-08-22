<?php

namespace App\Http\Controllers\Dashboard_Doctor;

use App\Http\Controllers\Controller;
use App\Models\MedicalCertificate;
use App\Models\Patient;
use App\Services\AuditLogService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        $doctor = Auth::guard('doctor')->user();

        $data = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'diagnostic_id' => 'nullable|exists:diagnostics,id',
            'type' => 'required|in:sick_leave,fitness,medical_report',
            'title' => 'required|string|max:200',
            'content' => 'required|string|min:20',
            'valid_from' => 'nullable|date',
            'valid_until' => 'nullable|date|after_or_equal:valid_from',
            'days_off' => 'nullable|integer|min:1|max:365',
        ]);

        $certificate = MedicalCertificate::create(array_merge($data, [
            'doctor_id' => $doctor->id,
            'reference_number' => MedicalCertificate::generateReference(),
            'issued_at' => now(),
        ]));

        AuditLogService::log('certificate_issued', $certificate);

        session()->flash('add');
        return redirect()->route('doctor.certificates.pdf', $certificate);
    }

    public function pdf(MedicalCertificate $certificate)
    {
        if ($certificate->doctor_id !== Auth::guard('doctor')->id()) {
            abort(403);
        }

        $certificate->load(['patient', 'doctor']);

        $pdf = Pdf::loadView('pdf.medical-certificate', compact('certificate'))->setPaper('a4');

        return $pdf->download('certificate-' . $certificate->reference_number . '.pdf');
    }
}
