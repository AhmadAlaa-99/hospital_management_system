<?php

namespace App\Http\Livewire;

use App\Events\CreateInvoice;
use App\Models\Doctor;
use App\Models\FundAccount;
use App\Models\Invoice;
use App\Models\Patient;
use App\Models\PatientAccount;
use App\Models\Service;
use App\Services\InsuranceClaimService;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class SingleInvoices extends Component
{
    public $InvoiceSaved = false;
    public $InvoiceUpdated = false;
    public $show_table = true;
    public $username;
    public $tax_rate = 17;
    public $updateMode = false;
    public $price;
    public $discount_value = 0;
    public $patient_id;
    public $doctor_id;
    public $section_id; // numeric section id
    public $section_name; // display name
    public $type;
    public $Service_id;
    public $single_invoice_id;
    public $catchError;
    public $insurance_note = '';

    public function mount()
    {
        $this->username = auth()->user()->name;
    }

    public function render()
    {
        $subtotal = ((is_numeric($this->price) ? $this->price : 0)) - ((is_numeric($this->discount_value) ? $this->discount_value : 0));
        $tax_value = $subtotal * ((is_numeric($this->tax_rate) ? $this->tax_rate : 0) / 100);

        return view('livewire.single_invoices.single-invoices', [
            'single_invoices' => Invoice::with(['Service', 'Patient', 'Doctor', 'Section'])->where('invoice_type', 1)->latest()->get(),
            'Patients' => Patient::all(),
            'Doctors' => Doctor::all(),
            'Services' => Service::all(),
            'subtotal' => $subtotal,
            'tax_value' => $tax_value,
        ]);
    }

    public function show_form_add()
    {
        $this->resetForm();
        $this->show_table = false;
        $this->updateMode = false;
    }

    public function show_form_table()
    {
        $this->resetForm();
        $this->show_table = true;
        $this->updateMode = false;
    }

    public function resetForm()
    {
        $this->reset([
            'price', 'discount_value', 'patient_id', 'doctor_id', 'section_id', 'section_name',
            'type', 'Service_id', 'single_invoice_id', 'catchError', 'InvoiceSaved', 'InvoiceUpdated',
            'insurance_note',
        ]);
        $this->discount_value = 0;
        $this->tax_rate = 17;
        $this->updateMode = false;
    }

    public function print($id)
    {
        return redirect()->route('Print_single_invoices', $id);
    }

    public function get_section()
    {
        $doctor = Doctor::with('section')->find($this->doctor_id);
        if (!$doctor || !$doctor->section) {
            $this->section_id = null;
            $this->section_name = '';
            return;
        }
        $this->section_id = $doctor->section->id;
        $this->section_name = $doctor->section->name;
    }

    public function get_price()
    {
        $service = Service::find($this->Service_id);
        $this->price = $service ? $service->price : 0;
        $this->applyInsuranceDiscount();
    }

    public function updatedPatientId()
    {
        $this->applyInsuranceDiscount();
    }

    public function applyInsuranceDiscount()
    {
        $this->insurance_note = '';

        if (!$this->patient_id || !is_numeric($this->price) || (float) $this->price <= 0) {
            return;
        }

        $patient = Patient::with('insurance')->find($this->patient_id);
        if (!$patient || !$patient->insurance || (int) $patient->insurance->status !== 1) {
            return;
        }

        $percentage = (float) $patient->insurance->discount_percentage;
        $this->discount_value = round(((float) $this->price * $percentage) / 100, 2);
        $this->insurance_note = 'تم تطبيق خصم التأمين: ' . $patient->insurance->name
            . ' (' . $percentage . '%) — تحمل الشركة: ' . $patient->insurance->Company_rate . '%';
    }

    public function edit($id)
    {
        $this->catchError = null;
        $this->InvoiceSaved = false;
        $this->InvoiceUpdated = false;
        $this->show_table = false;
        $this->updateMode = true;

        $single_invoice = Invoice::with('Section')->findOrFail($id);
        $this->single_invoice_id = $single_invoice->id;
        $this->patient_id = $single_invoice->patient_id;
        $this->doctor_id = $single_invoice->doctor_id;
        $this->section_id = $single_invoice->section_id;
        $this->section_name = optional($single_invoice->Section)->name;
        $this->Service_id = $single_invoice->Service_id;
        $this->price = $single_invoice->price;
        $this->discount_value = $single_invoice->discount_value;
        $this->tax_rate = $single_invoice->tax_rate ?: 17;
        $this->type = $single_invoice->type;
    }

    protected function resolveSectionId(): ?int
    {
        if (is_numeric($this->section_id)) {
            return (int) $this->section_id;
        }

        if ($this->doctor_id) {
            $doctor = Doctor::find($this->doctor_id);
            return $doctor ? (int) $doctor->section_id : null;
        }

        if ($this->section_name) {
            $row = DB::table('section_translations')->where('name', $this->section_name)->first();
            return $row ? (int) $row->section_id : null;
        }

        return null;
    }

    protected function calcTaxValue(): float
    {
        return ((float) $this->price - (float) $this->discount_value) * ((is_numeric($this->tax_rate) ? (float) $this->tax_rate : 0) / 100);
    }

    protected function calcTotal(): float
    {
        return (float) $this->price - (float) $this->discount_value + $this->calcTaxValue();
    }

    public function store()
    {
        $this->catchError = null;
        $this->InvoiceSaved = false;
        $this->InvoiceUpdated = false;

        if (!$this->patient_id || !$this->doctor_id || !$this->Service_id || !$this->type) {
            $this->catchError = 'يرجى تعبئة جميع الحقول المطلوبة (المريض، الطبيب، الخدمة، نوع الفاتورة).';
            return;
        }

        $sectionId = $this->resolveSectionId();
        if (!$sectionId) {
            $this->catchError = 'تعذر تحديد القسم. اختر الطبيب مرة أخرى.';
            return;
        }

        DB::beginTransaction();
        try {
            if ($this->updateMode) {
                $invoice = Invoice::findOrFail($this->single_invoice_id);
            } else {
                $invoice = new Invoice();
                $invoice->invoice_status = 1;
            }

            $invoice->invoice_type = 1;
            $invoice->invoice_date = date('Y-m-d');
            $invoice->patient_id = $this->patient_id;
            $invoice->doctor_id = $this->doctor_id;
            $invoice->section_id = $sectionId;
            $invoice->Service_id = $this->Service_id;
            $invoice->price = $this->price;
            $invoice->discount_value = $this->discount_value ?: 0;
            $invoice->tax_rate = $this->tax_rate;
            $invoice->tax_value = $this->calcTaxValue();
            $invoice->total_with_tax = $this->calcTotal();
            $invoice->type = $this->type;
            $invoice->save();

            InsuranceClaimService::createFromInvoice($invoice);

            if ((int) $this->type === 1) {
                $fund = FundAccount::firstOrNew(['invoice_id' => $invoice->id]);
                $fund->date = date('Y-m-d');
                $fund->invoice_id = $invoice->id;
                $fund->Debit = $invoice->total_with_tax;
                $fund->credit = 0.00;
                $fund->save();

                // remove patient account if switching to cash
                PatientAccount::where('invoice_id', $invoice->id)->delete();
            } else {
                $patientAccount = PatientAccount::firstOrNew(['invoice_id' => $invoice->id]);
                $patientAccount->date = date('Y-m-d');
                $patientAccount->invoice_id = $invoice->id;
                $patientAccount->patient_id = $invoice->patient_id;
                $patientAccount->Debit = $invoice->total_with_tax;
                $patientAccount->credit = 0.00;
                $patientAccount->save();

                FundAccount::where('invoice_id', $invoice->id)->delete();
            }

            if (!$this->updateMode) {
                $patient = Patient::find($this->patient_id);
                \App\Services\NotificationService::notifyDoctor(
                    (int) $this->doctor_id,
                    'كشف جديد : ' . optional($patient)->name
                );

                event(new CreateInvoice([
                    'patient' => $this->patient_id,
                    'invoice_id' => $invoice->id,
                    'doctor_id' => $this->doctor_id,
                ]));
                $this->InvoiceSaved = true;
            } else {
                $this->InvoiceUpdated = true;
            }

            DB::commit();
            $this->show_table = true;
            $this->updateMode = false;
        } catch (\Exception $e) {
            DB::rollBack();
            $this->catchError = \App\Helpers\FriendlyError::message($e->getMessage());
        }
    }

    public function delete($id)
    {
        $this->single_invoice_id = $id;
    }

    public function destroy()
    {
        Invoice::destroy($this->single_invoice_id);
        $this->show_table = true;
        $this->InvoiceUpdated = false;
        $this->InvoiceSaved = false;
    }
}
