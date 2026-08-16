<?php

namespace Database\Seeders;

use App\Models\Diagnostic;
use App\Models\Doctor;
use App\Models\FundAccount;
use App\Models\Group;
use App\Models\Image;
use App\Models\Invoice;
use App\Models\Laboratorie;
use App\Models\LaboratorieEmployee;
use App\Models\Notification;
use App\Models\Patient;
use App\Models\PatientAccount;
use App\Models\PaymentAccount;
use App\Models\Ray;
use App\Models\RayEmployee;
use App\Models\ReceiptAccount;
use App\Models\Service;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InvoiceDemoSeeder extends Seeder
{
    public function run()
    {
        DB::table('images')->whereIn('imageable_type', [
            'App\\Models\\Ray',
            'App\\Models\\Laboratorie',
        ])->delete();
        DB::table('notifications')->delete();
        DB::table('diagnostics')->delete();
        DB::table('rays')->delete();
        DB::table('laboratories')->delete();
        DB::table('fund_accounts')->delete();
        DB::table('patient_accounts')->delete();
        DB::table('receipt_accounts')->delete();
        DB::table('payment_accounts')->delete();
        DB::table('invoices')->delete();

        $patients = Patient::all();
        $doctors = Doctor::all();
        $services = Service::all();
        $groups = Group::all();
        $rayEmployees = RayEmployee::all();
        $labEmployees = LaboratorieEmployee::all();

        if ($patients->isEmpty() || $doctors->isEmpty() || $services->isEmpty()) {
            return;
        }

        $invoicePlans = [
            ['status' => 1, 'type' => 1, 'mode' => 'single', 'days' => -1],
            ['status' => 1, 'type' => 2, 'mode' => 'single', 'days' => -2],
            ['status' => 2, 'type' => 1, 'mode' => 'single', 'days' => -3],
            ['status' => 3, 'type' => 1, 'mode' => 'single', 'days' => -4],
            ['status' => 3, 'type' => 2, 'mode' => 'single', 'days' => -5],
            ['status' => 1, 'type' => 1, 'mode' => 'group', 'days' => -1],
            ['status' => 3, 'type' => 1, 'mode' => 'group', 'days' => -6],
            ['status' => 2, 'type' => 2, 'mode' => 'single', 'days' => -2],
            ['status' => 3, 'type' => 1, 'mode' => 'single', 'days' => -7],
            ['status' => 1, 'type' => 1, 'mode' => 'single', 'days' => 0],
            ['status' => 3, 'type' => 2, 'mode' => 'group', 'days' => -8],
            ['status' => 1, 'type' => 1, 'mode' => 'single', 'days' => -1],
        ];

        $createdInvoices = collect();

        foreach ($invoicePlans as $index => $plan) {
            $patient = $patients[$index % $patients->count()];
            $doctor = $doctors[$index % $doctors->count()];
            $date = now()->addDays($plan['days'])->toDateString();

            $invoice = new Invoice();
            $invoice->invoice_date = $date;
            $invoice->patient_id = $patient->id;
            $invoice->doctor_id = $doctor->id;
            $invoice->section_id = $doctor->section_id;
            $invoice->type = $plan['type'];
            $invoice->invoice_status = $plan['status'];

            if ($plan['mode'] === 'group' && $groups->isNotEmpty()) {
                $group = $groups[$index % $groups->count()];
                $invoice->invoice_type = 2;
                $invoice->Group_id = $group->id;
                $invoice->Service_id = null;
                $invoice->price = $group->Total_before_discount;
                $invoice->discount_value = $group->discount_value;
                $invoice->tax_rate = $group->tax_rate;
                $taxValue = ($group->Total_after_discount) * ((float) $group->tax_rate / 100);
                $invoice->tax_value = (string) round($taxValue, 2);
                $invoice->total_with_tax = $group->Total_with_tax;
            } else {
                $service = $services[$index % $services->count()];
                $discount = $index % 3 === 0 ? 5 : 0;
                $taxRate = 17;
                $afterDiscount = max($service->price - $discount, 0);
                $taxValue = $afterDiscount * ($taxRate / 100);

                $invoice->invoice_type = 1;
                $invoice->Service_id = $service->id;
                $invoice->Group_id = null;
                $invoice->price = $service->price;
                $invoice->discount_value = $discount;
                $invoice->tax_rate = (string) $taxRate;
                $invoice->tax_value = (string) round($taxValue, 2);
                $invoice->total_with_tax = $afterDiscount + $taxValue;
            }

            $invoice->save();
            $createdInvoices->push($invoice);

            if ((int) $invoice->type === 1) {
                $fund = new FundAccount();
                $fund->date = $date;
                $fund->invoice_id = $invoice->id;
                $fund->Debit = $invoice->total_with_tax;
                $fund->credit = 0;
                $fund->save();
            } else {
                $account = new PatientAccount();
                $account->date = $date;
                $account->patient_id = $patient->id;
                $account->invoice_id = $invoice->id;
                $account->Debit = $invoice->total_with_tax;
                $account->credit = 0;
                $account->save();
            }

            if ((int) $invoice->invoice_status === 3) {
                $diagnostic = new Diagnostic();
                $diagnostic->date = $date;
                $diagnostic->diagnosis = 'تشخيص تجريبي للمريض ' . $patient->name;
                $diagnostic->medicine = 'علاج داعم حسب الحالة + راحة لمدة 3 أيام';
                $diagnostic->invoice_id = $invoice->id;
                $diagnostic->patient_id = $patient->id;
                $diagnostic->doctor_id = $doctor->id;
                $diagnostic->save();
            }

            if ((int) $invoice->invoice_status === 1) {
                $notification = new Notification();
                $notification->reader_status = 0;
                $notification->user_id = $doctor->id;
                $notification->message = 'كشف جديد : ' . $patient->name;
                $notification->save();
            }
        }

        // Rays
        $raySamples = ['ray-sample-1.jpg', 'ray-sample-2.jpg', 'ray-sample-3.jpg'];
        foreach ($createdInvoices->take(6) as $index => $invoice) {
            $ray = new Ray();
            $ray->description = 'طلب أشعة تشخيصية للمريض';
            $ray->invoice_id = $invoice->id;
            $ray->patient_id = $invoice->patient_id;
            $ray->doctor_id = $invoice->doctor_id;
            $ray->employee_id = $rayEmployees->isNotEmpty() ? $rayEmployees[$index % $rayEmployees->count()]->id : null;
            $ray->description_employee = $index % 2 === 0 ? 'تم التصوير بنجاح ولا توجد ملاحظات حرجة' : null;
            $ray->case = $index % 2 === 0 ? 1 : 0;
            $ray->save();

            if ((int) $ray->case === 1) {
                $image = new Image();
                $image->filename = $raySamples[$index % count($raySamples)];
                $image->imageable_id = $ray->id;
                $image->imageable_type = 'App\Models\Ray';
                $image->save();
            }
        }

        // Laboratories
        $labSamples = ['lab-sample-1.jpg', 'lab-sample-2.jpg', 'lab-sample-3.jpg'];
        foreach ($createdInvoices->slice(3, 6)->values() as $index => $invoice) {
            $lab = new Laboratorie();
            $lab->description = 'طلب تحاليل مخبرية';
            $lab->invoice_id = $invoice->id;
            $lab->patient_id = $invoice->patient_id;
            $lab->doctor_id = $invoice->doctor_id;
            $lab->employee_id = $labEmployees->isNotEmpty() ? $labEmployees[$index % $labEmployees->count()]->id : null;
            $lab->description_employee = $index % 2 === 0 ? 'نتائج التحاليل ضمن المعدل الطبيعي' : null;
            $lab->case = $index % 2 === 0 ? 1 : 0;
            $lab->save();

            if ((int) $lab->case === 1) {
                $image = new Image();
                $image->filename = $labSamples[$index % count($labSamples)];
                $image->imageable_id = $lab->id;
                $image->imageable_type = 'App\Models\Laboratorie';
                $image->save();
            }
        }

        // Receipts (payments received from patients)
        foreach ($patients->take(4) as $index => $patient) {
            $amount = 50 + ($index * 25);
            $date = now()->subDays($index + 1)->toDateString();

            $receipt = new ReceiptAccount();
            $receipt->date = $date;
            $receipt->patient_id = $patient->id;
            $receipt->amount = $amount;
            $receipt->description = 'سند قبض تجريبي للمريض';
            $receipt->save();

            $fund = new FundAccount();
            $fund->date = $date;
            $fund->receipt_id = $receipt->id;
            $fund->Debit = $amount;
            $fund->credit = 0;
            $fund->save();

            $account = new PatientAccount();
            $account->date = $date;
            $account->patient_id = $patient->id;
            $account->receipt_id = $receipt->id;
            $account->Debit = 0;
            $account->credit = $amount;
            $account->save();
        }

        // Payments to patients
        foreach ($patients->take(3) as $index => $patient) {
            $amount = 20 + ($index * 10);
            $date = now()->subDays($index + 2)->toDateString();

            $payment = new PaymentAccount();
            $payment->date = $date;
            $payment->patient_id = $patient->id;
            $payment->amount = $amount;
            $payment->description = 'سند صرف تجريبي للمريض';
            $payment->save();

            $fund = new FundAccount();
            $fund->date = $date;
            $fund->Payment_id = $payment->id;
            $fund->Debit = 0;
            $fund->credit = $amount;
            $fund->save();

            $account = new PatientAccount();
            $account->date = $date;
            $account->patient_id = $patient->id;
            $account->Payment_id = $payment->id;
            $account->Debit = $amount;
            $account->credit = 0;
            $account->save();
        }
    }
}
