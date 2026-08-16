<?php

namespace App\Repository\doctor_dashboard;

use App\Interfaces\doctor_dashboard\DiagnosisRepositoryInterface;
use App\Models\Diagnostic;
use App\Models\Invoice;
use App\Models\Prescription;
use Illuminate\Support\Facades\DB;

class DiagnosisRepository implements DiagnosisRepositoryInterface
{
    public function store($request)
    {
        DB::beginTransaction();

        try {

            $this->invoice_status($request->invoice_id,3);
            $diagnosis = new Diagnostic();
            $diagnosis->date = date('Y-m-d');
            $diagnosis->diagnosis = $request->diagnosis;
            $diagnosis->medicine = $request->medicine;
            $diagnosis->invoice_id = $request->invoice_id;
            $diagnosis->patient_id = $request->patient_id;
            $diagnosis->doctor_id = $request->doctor_id;
            $diagnosis->save();

            $this->savePrescriptions($diagnosis, $request);

            DB::commit();
            session()->flash('add');
            return redirect()->back();
        }

        catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function show($id)
    {
        $patient_records = Diagnostic::where('patient_id',$id)->get();
        return view('Dashboard.Doctor.invoices.patient_record',compact('patient_records'));
    }

    public function addReview($request)
    {
        DB::beginTransaction();
        try {

            $this->invoice_status($request->invoice_id,2);
            $diagnosis = new Diagnostic();
            $diagnosis->date = date('Y-m-d');
            $diagnosis->review_date = date('Y-m-d H:i:s');
            $diagnosis->diagnosis = $request->diagnosis;
            $diagnosis->medicine = $request->medicine;
            $diagnosis->invoice_id = $request->invoice_id;
            $diagnosis->patient_id = $request->patient_id;
            $diagnosis->doctor_id = $request->doctor_id;
            $diagnosis->save();

            $this->savePrescriptions($diagnosis, $request);

            DB::commit();
            session()->flash('add');
            return redirect()->back();
        }

        catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }


    protected function savePrescriptions(Diagnostic $diagnosis, $request): void
    {
        if (!$request->has('medicines') || !is_array($request->medicines)) {
            return;
        }

        foreach ($request->medicines as $item) {
            if (empty($item['medicine_name'])) {
                continue;
            }
            Prescription::create([
                'diagnostic_id' => $diagnosis->id,
                'medicine_name' => $item['medicine_name'],
                'dosage' => $item['dosage'] ?? null,
                'frequency' => $item['frequency'] ?? null,
                'duration_days' => $item['duration_days'] ?? null,
                'instructions' => $item['instructions'] ?? null,
            ]);
        }
    }


    public function invoice_status($invoice_id,$id_status){
        $invoice_status = Invoice::findorFail($invoice_id);
        $invoice_status->update([
            'invoice_status'=>$id_status
        ]);
    }


}
