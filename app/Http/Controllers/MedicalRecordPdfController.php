<?php

namespace App\Http\Controllers;

use App\Models\Diagnostic;
use App\Models\Laboratorie;
use App\Models\Patient;
use App\Models\Prescription;
use App\Models\Ray;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class MedicalRecordPdfController extends Controller
{
    public function export(Request $request, $patientId)
    {
        $patient = Patient::findOrFail($patientId);
        $this->authorizePatientAccess($patient);

        $diagnostics = Diagnostic::with(['Doctor', 'prescriptions'])
            ->where('patient_id', $patientId)
            ->latest()
            ->get();

        $rays = Ray::where('patient_id', $patientId)->latest()->get();
        $labs = Laboratorie::where('patient_id', $patientId)->latest()->get();

        $pdf = Pdf::loadView('pdf.medical-record', compact('patient', 'diagnostics', 'rays', 'labs'))
            ->setPaper('a4');

        return $pdf->download('medical-record-' . $patientId . '.pdf');
    }

    protected function authorizePatientAccess(Patient $patient): void
    {
        if (auth('patient')->check() && auth('patient')->id() === $patient->id) {
            return;
        }

        if (auth('doctor')->check()) {
            return;
        }

        if (auth('admin')->check()) {
            return;
        }

        abort(403);
    }
}
