<?php

namespace App\Repository\doctor_dashboard;

use App\Helpers\FriendlyError;
use App\Interfaces\doctor_dashboard\DiagnosisRepositoryInterface;
use App\Models\Diagnostic;
use App\Models\Doctor;
use App\Models\FollowUpPlan;
use App\Models\Invoice;
use App\Models\Prescription;
use App\Services\AuditLogService;
use App\Services\FollowUpService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class DiagnosisRepository implements DiagnosisRepositoryInterface
{
    public function store($request)
    {
        DB::beginTransaction();

        try {

            $this->invoice_status($request->invoice_id, 3);
            $diagnosis = new Diagnostic();
            $diagnosis->date = date('Y-m-d');
            $diagnosis->diagnosis = $request->diagnosis;
            $diagnosis->medicine = $request->input('medicine', '');
            $diagnosis->invoice_id = $request->invoice_id;
            $diagnosis->patient_id = $request->patient_id;
            $diagnosis->doctor_id = $request->doctor_id;
            $diagnosis->save();

            $this->savePrescriptionsSafely($diagnosis, $request);
            $this->createFollowUpPlan($request, $diagnosis);

            DB::commit();
            session()->flash('add');
            return redirect()->back();
        }

        catch (\Exception $e) {
            DB::rollback();
            Log::error('Diagnosis store failed', [
                'invoice_id' => $request->invoice_id ?? null,
                'message' => $e->getMessage(),
            ]);

            return redirect()->back()
                ->withErrors(['error' => FriendlyError::message($e->getMessage())])
                ->withInput();
        }
    }

    public function show($id)
    {
        $patient_records = Diagnostic::where('patient_id',$id)->get();
        return view('Dashboard.doctor.invoices.patient_record', compact('patient_records'));
    }

    public function addReview($request)
    {
        DB::beginTransaction();
        try {

            $this->invoice_status($request->invoice_id,2);
            $diagnosis = new Diagnostic();
            $diagnosis->date = date('Y-m-d');
            $diagnosis->review_date = Carbon::parse($request->review_date)->setTime(9, 0, 0);
            if (Schema::hasColumn('diagnostics', 'review_reminder_sent')) {
                $diagnosis->review_reminder_sent = false;
            }
            $diagnosis->diagnosis = $request->diagnosis;
            $diagnosis->medicine = $request->input('medicine', '');
            $diagnosis->invoice_id = $request->invoice_id;
            $diagnosis->patient_id = $request->patient_id;
            $diagnosis->doctor_id = $request->doctor_id;
            $diagnosis->save();

            $this->savePrescriptionsSafely($diagnosis, $request);

            DB::commit();
            session()->flash('add');
            return redirect()->back();
        }

        catch (\Exception $e) {
            DB::rollback();
            Log::error('Diagnosis review failed', [
                'invoice_id' => $request->invoice_id ?? null,
                'message' => $e->getMessage(),
            ]);

            return redirect()->back()
                ->withErrors(['error' => FriendlyError::message($e->getMessage())])
                ->withInput();
        }
    }


    protected function savePrescriptionsSafely(Diagnostic $diagnosis, $request): void
    {
        try {
            $this->savePrescriptions($diagnosis, $request);
        } catch (\Throwable $e) {
            Log::warning('Prescriptions could not be saved', [
                'diagnostic_id' => $diagnosis->id,
                'message' => $e->getMessage(),
            ]);
        }
    }

    protected function savePrescriptions(Diagnostic $diagnosis, $request): void
    {
        if (!Schema::hasTable('prescriptions')) {
            return;
        }

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
                'dosage' => $this->nullableString($item['dosage'] ?? null),
                'frequency' => $this->nullableString($item['frequency'] ?? null),
                'duration_days' => $this->nullableInt($item['duration_days'] ?? null),
                'instructions' => $this->nullableString($item['instructions'] ?? null),
            ]);
        }
    }

    protected function createFollowUpPlan($request, Diagnostic $diagnosis): void
    {
        if (!$request->filled('follow_up_date') || !Schema::hasTable('follow_up_plans')) {
            return;
        }

        try {
            $doctor = Doctor::find($request->doctor_id);

            $plan = FollowUpPlan::create([
                'patient_id' => $request->patient_id,
                'doctor_id' => $request->doctor_id,
                'section_id' => $doctor ? $doctor->section_id : null,
                'diagnostic_id' => $diagnosis->id,
                'follow_up_date' => $request->follow_up_date,
                'notes' => $request->follow_up_notes,
                'status' => 'scheduled',
            ]);

            if (Schema::hasTable('activity_logs')) {
                AuditLogService::log('follow_up_created', $plan);
            }

            if ($request->boolean('create_follow_up_appointment')) {
                FollowUpService::createAppointmentForPlan($plan);
            }
        } catch (\Throwable $e) {
            Log::warning('Follow-up plan could not be saved', [
                'diagnostic_id' => $diagnosis->id,
                'message' => $e->getMessage(),
            ]);
        }
    }

    protected function nullableString($value): ?string
    {
        return ($value === null || $value === '') ? null : (string) $value;
    }

    protected function nullableInt($value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }

        return (int) $value;
    }


    public function invoice_status($invoice_id,$id_status){
        $invoice_status = Invoice::findorFail($invoice_id);
        $invoice_status->update([
            'invoice_status'=>$id_status
        ]);
    }


}
