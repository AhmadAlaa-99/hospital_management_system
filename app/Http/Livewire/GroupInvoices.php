<?php

namespace App\Http\Livewire;

use App\Models\Doctor;
use App\Models\FundAccount;
use App\Models\Group;
use App\Models\Invoice;
use App\Models\Patient;
use App\Models\PatientAccount;
use App\Services\InsuranceClaimService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Livewire\Component;

class GroupInvoices extends Component
{
    public $InvoiceSaved = false;
    public $InvoiceUpdated = false;
    public $show_table = true;
    public $updateMode = false;
    public $group_invoice_id;
    public $Group_id;
    public $catchError;
    public $price = 0;
    public $patient_id,$doctor_id,$section_id,$type;
    public $discount_value = 0;
    public $tax_rate = 0;
    public $section_name;

    public $insurance_note = '';

    public function render()
    {
        return view('livewire.group_invoices.group-invoices', [
            'group_invoices'=>Invoice::with(['Group','Patient','Doctor','Section'])->where('invoice_type',2)->latest()->get(),
            'Patients'=>Patient::all(),
            'Doctors'=>Doctor::all(),
            'Groups'=>Group::all(),
            'subtotal' => $Total_after_discount = ((is_numeric($this->price) ? $this->price : 0)) - ((is_numeric($this->discount_value) ? $this->discount_value : 0)),
            'tax_value'=> $Total_after_discount * ((is_numeric($this->tax_rate) ? $this->tax_rate : 0) / 100)
        ]);
    }


    public function show_form_add(){
        $this->resetFormState();
        $this->show_table = false;
        $this->updateMode = false;
    }

    public function show_form_table()
    {
        $this->resetFormState();
        $this->show_table = true;
        $this->updateMode = false;
    }

    protected function resetFormState()
    {
        $this->Group_id = null;
        $this->group_invoice_id = null;
        $this->patient_id = null;
        $this->doctor_id = null;
        $this->section_id = null;
        $this->section_name = null;
        $this->type = null;
        $this->price = 0;
        $this->discount_value = 0;
        $this->tax_rate = 0;
        $this->catchError = null;
        $this->InvoiceSaved = false;
        $this->InvoiceUpdated = false;
        $this->insurance_note = '';
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
        $group = Group::find($this->Group_id);
        if (!$group) {
            return;
        }
        $this->price = $group->Total_before_discount;
        $this->discount_value = $group->discount_value;
        $this->tax_rate = $group->tax_rate;
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

        $baseDiscount = 0;
        if ($this->Group_id) {
            $group = Group::find($this->Group_id);
            $baseDiscount = $group ? (float) $group->discount_value : 0;
        }

        $priceAfterGroupDiscount = (float) $this->price - $baseDiscount;
        $percentage = (float) $patient->insurance->discount_percentage;
        $insuranceDiscount = round(($priceAfterGroupDiscount * $percentage) / 100, 2);
        $this->discount_value = $baseDiscount + $insuranceDiscount;
        $this->insurance_note = 'تم تطبيق خصم التأمين: ' . $patient->insurance->name
            . ' (' . $percentage . '%) — تحمل الشركة: ' . $patient->insurance->Company_rate . '%';
    }


    public function store()
    {

        // في حالة كانت الفاتورة نقدي
        if($this->type == 1){

            try {
                // في حالة التعديل
                if($this->updateMode){

                    $group_invoices = Invoice::findorfail($this->group_invoice_id);
                    $group_invoices->invoice_type = 2;
                    $group_invoices->invoice_date = date('Y-m-d');
                    $group_invoices->patient_id = $this->patient_id;
                    $group_invoices->doctor_id = $this->doctor_id;
                    $group_invoices->section_id = is_numeric($this->section_id)
                        ? (int) $this->section_id
                        : optional(DB::table('section_translations')->where('name', $this->section_name ?: $this->section_id)->first())->section_id;
                    $group_invoices->Group_id = $this->Group_id;
                    $group_invoices->price = $this->price;
                    $group_invoices->discount_value = $this->discount_value;
                    $group_invoices->tax_rate = $this->tax_rate;
                    // قيمة الضريبة = السعر - الخصم * نسبة الضريبة /100
                    $group_invoices->tax_value = ($this->price -$this->discount_value) * ((is_numeric($this->tax_rate) ? $this->tax_rate : 0) / 100);
                    // الاجمالي شامل الضريبة  = السعر - الخصم + قيمة الضريبة
                    $group_invoices->total_with_tax = $group_invoices->price -  $group_invoices->discount_value + $group_invoices->tax_value;
                    $group_invoices->type = $this->type;
                    $group_invoices->save();

                    InsuranceClaimService::createFromInvoice($group_invoices);

                    $fund_accounts = FundAccount::where('invoice_id',$this->group_invoice_id)->first();
                    $fund_accounts->date = date('Y-m-d');
                    $fund_accounts->invoice_id = $group_invoices->id;
                    $fund_accounts->Debit = $group_invoices->total_with_tax;
                    $fund_accounts->credit = 0.00;
                    $fund_accounts->save();
                    $this->InvoiceUpdated =true;
                    $this->show_table =true;

                }

                // في حالة الاضافة
                else{

                    $group_invoices = new Invoice();
                    $group_invoices->invoice_type = 2;
                    $group_invoices->invoice_date = date('Y-m-d');
                    $group_invoices->patient_id = $this->patient_id;
                    $group_invoices->doctor_id = $this->doctor_id;
                    $group_invoices->section_id = is_numeric($this->section_id)
                        ? (int) $this->section_id
                        : optional(DB::table('section_translations')->where('name', $this->section_name ?: $this->section_id)->first())->section_id;
                    $group_invoices->Group_id = $this->Group_id;
                    $group_invoices->price = $this->price;
                    $group_invoices->discount_value = $this->discount_value;
                    $group_invoices->tax_rate = $this->tax_rate;
                    // قيمة الضريبة = السعر - الخصم * نسبة الضريبة /100
                    $group_invoices->tax_value = ($this->price - $this->discount_value) * ((is_numeric($this->tax_rate) ? $this->tax_rate : 0) / 100);
                    // الاجمالي شامل الضريبة  = السعر - الخصم + قيمة الضريبة
                    $group_invoices->total_with_tax = $group_invoices->price -  $group_invoices->discount_value + $group_invoices->tax_value;
                    $group_invoices->type = $this->type;
                    $group_invoices->save();

                    InsuranceClaimService::createFromInvoice($group_invoices);

                    $fund_accounts = new FundAccount();
                    $fund_accounts->date = date('Y-m-d');
                    $fund_accounts->invoice_id = $group_invoices->id;
                    $fund_accounts->Debit = $group_invoices->total_with_tax;
                    $fund_accounts->credit = 0.00;
                    $fund_accounts->save();
                    $this->InvoiceSaved =true;
                    $this->show_table =true;
                    $this->rest();
                }

            }


            catch (\Exception $e) {
                $this->catchError = \App\Helpers\FriendlyError::message($e->getMessage());
            }

        }

//----------------------------------------------------------------------------------------------------

        // في حالة الفاتورة اجل

        else{

            try {
                // في حالة التعديل
                if($this->updateMode){

                    $group_invoices = Invoice::findorfail($this->group_invoice_id);
                    $group_invoices->invoice_type = 2;
                    $group_invoices->invoice_date = date('Y-m-d');
                    $group_invoices->patient_id = $this->patient_id;
                    $group_invoices->doctor_id = $this->doctor_id;
                    $group_invoices->section_id = is_numeric($this->section_id)
                        ? (int) $this->section_id
                        : optional(DB::table('section_translations')->where('name', $this->section_name ?: $this->section_id)->first())->section_id;
                    $group_invoices->Group_id = $this->Group_id;
                    $group_invoices->price = $this->price;
                    $group_invoices->discount_value = $this->discount_value;
                    $group_invoices->tax_rate = $this->tax_rate;
                    // قيمة الضريبة = السعر - الخصم * نسبة الضريبة /100
                    $group_invoices->tax_value = ($this->price -$this->discount_value) * ((is_numeric($this->tax_rate) ? $this->tax_rate : 0) / 100);
                    // الاجمالي شامل الضريبة  = السعر - الخصم + قيمة الضريبة
                    $group_invoices->total_with_tax = $group_invoices->price -  $group_invoices->discount_value + $group_invoices->tax_value;
                    $group_invoices->type = $this->type;
                    $group_invoices->save();

                    InsuranceClaimService::createFromInvoice($group_invoices);

                    $patient_accounts = PatientAccount::where('invoice_id',$this->group_invoice_id)->first();
                    $patient_accounts->date = date('Y-m-d');
                    $patient_accounts->invoice_id = $group_invoices->id;
                    $patient_accounts->patient_id = $group_invoices->patient_id;
                    $patient_accounts->Debit = $group_invoices->total_with_tax;
                    $patient_accounts->credit = 0.00;
                    $patient_accounts->save();
                    $this->InvoiceUpdated =true;
                    $this->show_table =true;
                    $this->rest();

                }

                // في حالة الاضافة
                else{


                    $group_invoices = new Invoice();
                    $group_invoices->invoice_type = 2;
                    $group_invoices->invoice_date = date('Y-m-d');
                    $group_invoices->patient_id = $this->patient_id;
                    $group_invoices->doctor_id = $this->doctor_id;
                    $group_invoices->section_id = is_numeric($this->section_id)
                        ? (int) $this->section_id
                        : optional(DB::table('section_translations')->where('name', $this->section_name ?: $this->section_id)->first())->section_id;
                    $group_invoices->Group_id = $this->Group_id;
                    $group_invoices->price = $this->price;
                    $group_invoices->discount_value = $this->discount_value;
                    $group_invoices->tax_rate = $this->tax_rate;
                    // قيمة الضريبة = السعر - الخصم * نسبة الضريبة /100
                    $group_invoices->tax_value = ($this->price -$this->discount_value) * ((is_numeric($this->tax_rate) ? $this->tax_rate : 0) / 100);
                    // الاجمالي شامل الضريبة  = السعر - الخصم + قيمة الضريبة
                    $group_invoices->total_with_tax = $group_invoices->price -  $group_invoices->discount_value + $group_invoices->tax_value;
                    $group_invoices->type = $this->type;
                    $group_invoices->save();

                    InsuranceClaimService::createFromInvoice($group_invoices);

                    $patient_accounts = new PatientAccount();
                    $patient_accounts->date = date('Y-m-d');
                    $patient_accounts->invoice_id = $group_invoices->id;
                    $patient_accounts->patient_id = $group_invoices->patient_id;
                    $patient_accounts->Debit = $group_invoices->total_with_tax;
                    $patient_accounts->credit = 0.00;
                    $patient_accounts->save();
                    $this->InvoiceSaved =true;
                    $this->show_table =true;
                    $this->rest();
                }

            }

            catch (\Exception $e) {
                $this->catchError = \App\Helpers\FriendlyError::message($e->getMessage());
            }
        }
    }


    public function edit($id){

        $this->show_table = false;
        $this->updateMode = true;
        $group_invoices = Invoice::with('Section')->findorfail($id);
        $this->group_invoice_id = $group_invoices->id;
        $this->patient_id = $group_invoices->patient_id;
        $this->doctor_id = $group_invoices->doctor_id;
        $this->section_id = $group_invoices->section_id;
        $this->section_name = optional($group_invoices->Section)->name;
        $this->Group_id = $group_invoices->Group_id;
        $this->price = $group_invoices->price;
        $this->discount_value = $group_invoices->discount_value;
        $this->tax_rate = $group_invoices->tax_rate;
        $this->type = $group_invoices->type;

    }

    public function delete($id){
        $this->group_invoice_id = $id;
    }

    public function destroy(){
        if ($this->group_invoice_id) {
            Invoice::destroy($this->group_invoice_id);
        }
        $this->group_invoice_id = null;
        $this->show_table = true;
        $this->dispatchBrowserEvent('hms-close-modal', ['modalId' => 'delete_invoice']);
    }

    public function rest()
    {
        $this->resetFormState();
        $this->updateMode = false;
    }

    public function print($id)
    {
        return Redirect::route('group_Print_single_invoices', $id);
    }
}
