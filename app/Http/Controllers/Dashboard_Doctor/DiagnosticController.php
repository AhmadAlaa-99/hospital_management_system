<?php

namespace App\Http\Controllers\Dashboard_Doctor;

use App\Http\Controllers\Controller;
use App\Interfaces\doctor_dashboard\DiagnosisRepositoryInterface;
use Illuminate\Http\Request;

class DiagnosticController extends Controller
{

    private $Diagnosis;

    public function __construct(DiagnosisRepositoryInterface $Diagnosis)
    {
        $this->Diagnosis = $Diagnosis;
    }

    public function index()
    {
        //
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        $request->validate([
            'invoice_id' => 'required|exists:invoices,id',
            'patient_id' => 'required|exists:patients,id',
            'doctor_id' => 'required|exists:doctors,id',
            'diagnosis' => 'required|string|min:2',
            'medicine' => 'nullable|string',
            'follow_up_date' => 'nullable|date|after_or_equal:today',
            'follow_up_notes' => 'nullable|string|max:500',
            'medicines' => 'nullable|array',
            'medicines.*.medicine_name' => 'nullable|string|max:255',
            'medicines.*.duration_days' => 'nullable|integer|min:1|max:3650',
        ], [
            'diagnosis.required' => 'يرجى إدخال التشخيص.',
            'diagnosis.min' => 'التشخيص قصير جداً.',
            'follow_up_date.after_or_equal' => 'تاريخ المتابعة يجب أن يكون اليوم أو بعده.',
        ]);

        return $this->Diagnosis->store($request);
    }

    public function addReview (Request $request)
    {
        $request->validate([
            'invoice_id' => 'required|exists:invoices,id',
            'patient_id' => 'required|exists:patients,id',
            'doctor_id' => 'required|exists:doctors,id',
            'review_date' => 'required|date|after_or_equal:today',
            'diagnosis' => 'nullable|string',
            'medicine' => 'nullable|string',
        ], [
            'review_date.required' => 'يرجى تحديد تاريخ المراجعة.',
            'review_date.after_or_equal' => 'تاريخ المراجعة يجب أن يكون اليوم أو بعده.',
        ]);

        return $this->Diagnosis->addReview($request);
    }


    public function show($id)
    {
        return $this->Diagnosis->show($id);
    }


    public function edit($id)
    {
        //
    }


    public function update(Request $request, $id)
    {
        //
    }


    public function destroy($id)
    {
        //
    }
}
