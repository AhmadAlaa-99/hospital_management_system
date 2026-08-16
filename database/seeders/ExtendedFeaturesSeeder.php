<?php

namespace Database\Seeders;

use App\Models\Ambulance;
use App\Models\AmbulanceRequest;
use App\Models\Appointment;
use App\Models\Diagnostic;
use App\Models\DoctorRating;
use App\Models\Insurance;
use App\Models\InsuranceClaim;
use App\Models\Invoice;
use App\Models\Patient;
use App\Models\Prescription;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Demo data for advanced HMS features:
 * schedules, e-Prescription, insurance claims, doctor ratings, ambulance requests.
 */
class ExtendedFeaturesSeeder extends Seeder
{
    public function run()
    {
        DB::table('prescriptions')->delete();
        DB::table('insurance_claims')->delete();
        DB::table('doctor_ratings')->delete();
        DB::table('ambulance_requests')->delete();

        $this->call(DoctorScheduleSeeder::class);
        $this->assignPatientInsurance();
        $this->seedPrescriptions();
        $this->seedInsuranceClaims();
        $this->seedDoctorRatings();
        $this->seedAmbulanceRequests();
    }

    protected function assignPatientInsurance(): void
    {
        $insurances = Insurance::where('status', 1)->get();
        if ($insurances->isEmpty()) {
            return;
        }

        Patient::all()->each(function (Patient $patient, int $index) use ($insurances) {
            if ($index < 4) {
                $patient->insurance_id = $insurances[$index % $insurances->count()]->id;
                $patient->save();
            }
        });
    }

    protected function seedPrescriptions(): void
    {
        $templates = [
            [
                'medicine_name' => 'باراسيتامول',
                'dosage' => '500 مج',
                'frequency' => '3 مرات يومياً',
                'duration_days' => 5,
                'instructions' => 'بعد الأكل — لا تتجاوز 3 جرام يومياً',
            ],
            [
                'medicine_name' => 'أموكسيسيلين',
                'dosage' => '500 مج',
                'frequency' => 'كل 8 ساعات',
                'duration_days' => 7,
                'instructions' => 'أكمل دورة العلاج كاملة',
            ],
            [
                'medicine_name' => 'أوميبرازول',
                'dosage' => '20 مج',
                'frequency' => 'مرة قبل الإفطار',
                'duration_days' => 14,
                'instructions' => 'على معدة فارغة',
            ],
        ];

        Diagnostic::all()->each(function (Diagnostic $diagnostic, int $index) use ($templates) {
            $count = $index % 2 === 0 ? 2 : 3;
            foreach (array_slice($templates, 0, $count) as $tpl) {
                Prescription::create(array_merge($tpl, ['diagnostic_id' => $diagnostic->id]));
            }
        });
    }

    protected function seedInsuranceClaims(): void
    {
        $insuredPatients = Patient::whereNotNull('insurance_id')->get();
        if ($insuredPatients->isEmpty()) {
            return;
        }

        $statuses = ['pending', 'approved', 'paid', 'rejected'];

        Invoice::with('patient')->get()->each(function (Invoice $invoice, int $index) use ($insuredPatients, $statuses) {
            $patient = $insuredPatients[$index % $insuredPatients->count()];
            if (!$patient->insurance_id) {
                return;
            }

            $total = (float) $invoice->total_with_tax;
            $discount = (float) ($invoice->discount_value ?? 0);
            $afterDiscount = max($total - $discount, 0);
            $companyRate = 0.6;
            $companyAmount = round($afterDiscount * $companyRate, 2);
            $patientAmount = round($afterDiscount - $companyAmount, 2);

            InsuranceClaim::create([
                'patient_id' => $patient->id,
                'insurance_id' => $patient->insurance_id,
                'invoice_id' => $invoice->id,
                'total_amount' => $total,
                'discount_amount' => $discount,
                'company_amount' => $companyAmount,
                'patient_amount' => $patientAmount,
                'status' => $statuses[$index % count($statuses)],
                'claim_date' => $invoice->invoice_date ?? now()->toDateString(),
                'notes' => 'مطالبة تجريبية — فاتورة #' . $invoice->id,
            ]);
        });
    }

    protected function seedDoctorRatings(): void
    {
        $finished = Appointment::where('type', 'منتهي')->get();
        $patients = Patient::all();
        if ($finished->isEmpty() || $patients->isEmpty()) {
            return;
        }

        $comments = [
            'تجربتي في المستشفى كانت ممتازة. حجزت الموعد بسهولة، والاستقبال سريع، والطبيب شرح لي الحالة بوضوح.',
            'المختبر والأشعة منظمين جداً، والنتائج وصلت بسرعة. الكادر محترم ويعامل المريض باهتمام حقيقي.',
            'راجعت العيادة والمتابعة كانت دقيقة ومطمئنة. المواعيد واضحة والتنبيهات تصل على الهاتف.',
            'من الحجز حتى انتهاء الكشف، كل شيء منظم. الأطباء متخصصون والمكان نظيف ومجهز.',
        ];

        foreach ($finished->take(4) as $index => $appointment) {
            $shareOnHomepage = $index < 4;
            DoctorRating::create([
                'doctor_id' => $appointment->doctor_id,
                'patient_id' => $patients[$index % $patients->count()]->id,
                'appointment_id' => $appointment->id,
                'rating' => [5, 4, 5, 5][$index % 4],
                'comment' => $comments[$index % count($comments)],
                'share_on_homepage' => $shareOnHomepage,
                'homepage_status' => $shareOnHomepage
                    ? DoctorRating::HOMEPAGE_APPROVED
                    : DoctorRating::HOMEPAGE_NONE,
            ]);
        }
    }

    protected function seedAmbulanceRequests(): void
    {
        $ambulance = Ambulance::where('is_available', 1)->first();

        AmbulanceRequest::create([
            'patient_name' => 'خالد عبد الرحمن',
            'phone' => '0593555001',
            'address' => 'دمشق — المزة — مقابل حديقة الجلاء',
            'notes' => 'حالة طوارئ — ألم صدر حاد',
            'status' => 'pending',
            'requested_at' => now()->subMinutes(15),
        ]);

        AmbulanceRequest::create([
            'patient_name' => 'سمير جمعه',
            'phone' => '0593555002',
            'address' => 'دمشق — كفر سوسة — بناء 12',
            'notes' => 'سقوط — إصابة في الركبة',
            'ambulance_id' => optional($ambulance)->id,
            'status' => 'dispatched',
            'requested_at' => now()->subHour(),
        ]);

        AmbulanceRequest::create([
            'patient_name' => 'نبيل حداد',
            'phone' => '0593555003',
            'address' => 'ريف دمشق — بلدة جديدة',
            'notes' => 'نقل مجدول — مريض كبار سن',
            'ambulance_id' => optional($ambulance)->id,
            'status' => 'completed',
            'requested_at' => now()->subHours(3),
        ]);

        AmbulanceRequest::create([
            'patient_name' => 'لينا سعيد',
            'phone' => '0593555004',
            'address' => 'دمشق — حي الشaghour القديم',
            'notes' => 'تم الإلغاء — تحسنت الحالة',
            'status' => 'cancelled',
            'requested_at' => now()->subHours(5),
        ]);
    }
}
