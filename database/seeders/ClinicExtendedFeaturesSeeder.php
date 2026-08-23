<?php

namespace Database\Seeders;

use App\Models\ActivityLog;
use App\Models\Ambulance;
use App\Models\AmbulanceRequest;
use App\Models\AmbulanceRequestTimeline;
use App\Models\Appointment;
use App\Models\Diagnostic;
use App\Models\Doctor;
use App\Models\ExternalRecord;
use App\Models\FollowUpPlan;
use App\Models\Group;
use App\Models\MedicalCertificate;
use App\Models\Medicine;
use App\Models\Patient;
use App\Models\PatientPackageUsage;
use App\Models\PharmacyDispensing;
use App\Models\PharmacyInvoice;
use App\Models\Prescription;
use App\Models\Referral;
use App\Models\Section;
use App\Models\ShamCashPayment;
use App\Models\SiteSetting;
use App\Models\Invoice;
use App\Services\AmbulanceWorkflowService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * بيانات تجريبية شاملة للميزات الجديدة:
 * تحويلات، متابعة، شهادات، إسعاف (triage + timeline)، صيدلية، باقات، ملفات، سجل نشاط.
 */
class ClinicExtendedFeaturesSeeder extends Seeder
{
    public function run()
    {
        $this->clearTables();
        $this->upgradeAmbulanceFleet();
        $this->upgradeHealthPackages();
        $this->seedTelemedicineAppointments();
        $this->seedReferrals();
        $this->seedFollowUpPlans();
        $this->seedMedicalCertificates();
        $this->seedAmbulanceWithTriageAndTimeline();
        $this->seedMedicinesAndDispensings();
        $this->seedPatientPackageUsages();
        $this->seedExternalRecords();
        $this->seedShamCashDemo();
        $this->seedActivityLogs();
    }

    protected function clearTables(): void
    {
        DB::table('pharmacy_dispensings')->delete();
        DB::table('pharmacy_invoices')->delete();
        DB::table('sham_cash_payments')->delete();
        DB::table('medicines')->delete();
        DB::table('activity_logs')->delete();
        DB::table('external_records')->delete();
        DB::table('patient_package_usages')->delete();
        DB::table('ambulance_request_timelines')->delete();
        DB::table('ambulance_requests')->delete();
        DB::table('medical_certificates')->delete();
        DB::table('follow_up_plans')->delete();
        DB::table('referrals')->delete();
    }

    protected function upgradeAmbulanceFleet(): void
    {
        $fleet = [
            ['car_number' => 'AMB-101', 'paramedic_name' => 'مسعف خالد', 'coverage_area' => 'دمشق — المزة', 'last_maintenance_date' => now()->subMonths(2)->toDateString()],
            ['car_number' => 'AMB-202', 'paramedic_name' => 'مسعفة سمر', 'coverage_area' => 'دمشق — كفر سوسة', 'last_maintenance_date' => now()->subMonths(4)->toDateString()],
            ['car_number' => 'AMB-303', 'paramedic_name' => 'مسعف نادر', 'coverage_area' => 'ريف دمشق', 'last_maintenance_date' => now()->subMonths(1)->toDateString()],
        ];

        foreach ($fleet as $data) {
            Ambulance::where('car_number', $data['car_number'])->update([
                'paramedic_name' => $data['paramedic_name'],
                'coverage_area' => $data['coverage_area'],
                'last_maintenance_date' => $data['last_maintenance_date'],
            ]);
        }
    }

    protected function upgradeHealthPackages(): void
    {
        $packages = [
            'باقة فحص شامل' => ['package_type' => 'general_checkup', 'validity_days' => 90],
            'باقة صحة القلب' => ['package_type' => 'cardiology', 'validity_days' => 60],
            'باقة الجهاز الهضمي' => ['package_type' => 'gastro', 'validity_days' => 45],
        ];

        Group::all()->each(function (Group $group) use ($packages) {
            $config = $packages[$group->name] ?? ['package_type' => 'general', 'validity_days' => 90];
            $group->update([
                'is_health_package' => true,
                'package_type' => $config['package_type'],
                'validity_days' => $config['validity_days'],
            ]);
        });
    }

    protected function seedTelemedicineAppointments(): void
    {
        $doctors = Doctor::where('status', 1)->take(3)->get();
        if ($doctors->isEmpty()) {
            return;
        }

        $telemedicine = [
            ['name' => 'محمد السيد', 'email' => 'patient@yahoo.com', 'phone' => '0592000001', 'notes' => 'متابعة ضغط عن بُعد', 'days' => 2],
            ['name' => 'فاطمة علي', 'email' => 'fatima.ali@demo.com', 'phone' => '0592000002', 'notes' => 'استشارة أطفال عن بُعد', 'days' => 3],
            ['name' => 'حسن عمر', 'email' => 'hassan.omar@demo.com', 'phone' => '0592000003', 'notes' => 'متابعة سكري', 'days' => 1],
        ];

        foreach ($telemedicine as $index => $data) {
            $doctor = $doctors[$index % $doctors->count()];
            Appointment::create([
                'doctor_id' => $doctor->id,
                'section_id' => $doctor->section_id,
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'notes' => $data['notes'],
                'type' => 'مؤكد',
                'appointment' => now()->addDays($data['days'])->setTime(11, 0),
                'preferred_date' => now()->addDays($data['days'])->toDateString(),
                'preferred_time' => '11:00:00',
                'consultation_type' => 'telemedicine',
                'meeting_url' => 'https://meet.jit.si/hms-demo-' . Str::random(8),
            ]);
        }

        // موعد طوارئ من الإسعاف (يُربط لاحقاً)
        $emergencyDoctor = Doctor::whereHas('section', function ($q) {
            $q->whereTranslationLike('name', '%باطنة%');
        })->first() ?? Doctor::first();

        if ($emergencyDoctor) {
            Appointment::create([
                'doctor_id' => $emergencyDoctor->id,
                'section_id' => $emergencyDoctor->section_id,
                'name' => 'خالد عبد الرحمن',
                'email' => 'emergency.transfer@demo.local',
                'phone' => '0593555001',
                'notes' => 'تحويل من الإسعاف — ألم صدر',
                'type' => 'غير مؤكد',
                'preferred_date' => now()->toDateString(),
                'preferred_time' => now()->format('H:i:s'),
                'is_emergency' => true,
                'consultation_type' => 'in_person',
            ]);
        }
    }

    protected function seedReferrals(): void
    {
        $patients = Patient::take(4)->get();
        $doctors = Doctor::where('status', 1)->get();
        if ($patients->count() < 2 || $doctors->count() < 4) {
            return;
        }

        $cardio = $doctors->first(fn ($d) => str_contains($d->name ?? '', 'قلب') || $d->section_id === 1) ?? $doctors[0];
        $eye = $doctors->first(fn ($d) => str_contains($d->name ?? '', 'عيون')) ?? $doctors[1];
        $neuro = $doctors->first(fn ($d) => str_contains($d->name ?? '', 'مخ')) ?? $doctors[2];
        $gastro = $doctors->first(fn ($d) => str_contains($d->name ?? '', 'باطنة') || str_contains($d->name ?? '', 'طارق')) ?? $doctors[3];

        $diagnostic = Diagnostic::first();

        $referrals = [
            [
                'patient_id' => $patients[0]->id,
                'from_doctor_id' => $cardio->id,
                'to_doctor_id' => $eye->id,
                'from_section_id' => $cardio->section_id,
                'to_section_id' => $eye->section_id,
                'diagnostic_id' => optional($diagnostic)->id,
                'reason' => 'المريض يعاني من تشوش رؤية مصاحب لألم الصدر — يحتاج فحص عيون تخصصي.',
                'notes' => 'يرجى فحص قاع العين',
                'status' => 'completed',
                'accepted_at' => now()->subDays(3),
                'completed_at' => now()->subDays(2),
            ],
            [
                'patient_id' => $patients[1]->id,
                'from_doctor_id' => $gastro->id,
                'to_doctor_id' => $cardio->id,
                'from_section_id' => $gastro->section_id,
                'to_section_id' => $cardio->section_id,
                'diagnostic_id' => null,
                'reason' => 'ألم بطن مع خفقان — للاستبعاد مشاكل قلبية.',
                'notes' => null,
                'status' => 'accepted',
                'accepted_at' => now()->subDay(),
                'completed_at' => null,
            ],
            [
                'patient_id' => $patients[2]->id,
                'from_doctor_id' => $eye->id,
                'to_doctor_id' => $neuro->id,
                'from_section_id' => $eye->section_id,
                'to_section_id' => $neuro->section_id,
                'diagnostic_id' => null,
                'reason' => 'صداع مزمن مع ضعف رؤية — تحويل لعيادة مخ وأعصاب.',
                'notes' => 'يُفضّل رنين مغناطيسي',
                'status' => 'pending',
                'accepted_at' => null,
                'completed_at' => null,
            ],
            [
                'patient_id' => $patients[3]->id,
                'from_doctor_id' => $cardio->id,
                'to_doctor_id' => $gastro->id,
                'from_section_id' => $cardio->section_id,
                'to_section_id' => $gastro->section_id,
                'diagnostic_id' => null,
                'reason' => 'حرقة معدة متكررة — تحويل لباطنة.',
                'notes' => null,
                'status' => 'rejected',
                'accepted_at' => null,
                'completed_at' => null,
            ],
        ];

        foreach ($referrals as $data) {
            Referral::create($data);
        }
    }

    protected function seedFollowUpPlans(): void
    {
        $patients = Patient::take(5)->get();
        $doctors = Doctor::where('status', 1)->take(3)->get();
        $diagnostics = Diagnostic::take(3)->get();

        if ($patients->isEmpty() || $doctors->isEmpty()) {
            return;
        }

        $plans = [
            ['days' => 7, 'status' => 'scheduled', 'notes' => 'مراجعة بعد أسبوع — فحص ضغط'],
            ['days' => 14, 'status' => 'scheduled', 'notes' => 'متابعة نتائج تحليل'],
            ['days' => -3, 'status' => 'completed', 'notes' => 'تمت المتابعة بنجاح'],
            ['days' => -10, 'status' => 'missed', 'notes' => 'لم يحضر المريض'],
            ['days' => 21, 'status' => 'scheduled', 'notes' => 'متابعة سكري — HbA1c'],
        ];

        foreach ($plans as $index => $plan) {
            $doctor = $doctors[$index % $doctors->count()];
            $patient = $patients[$index % $patients->count()];
            $diagnostic = $diagnostics[$index % max($diagnostics->count(), 1)] ?? null;

            FollowUpPlan::create([
                'patient_id' => $patient->id,
                'doctor_id' => $doctor->id,
                'section_id' => $doctor->section_id,
                'diagnostic_id' => optional($diagnostic)->id,
                'follow_up_date' => now()->addDays($plan['days'])->toDateString(),
                'notes' => $plan['notes'],
                'status' => $plan['status'],
                'reminder_sent' => $plan['days'] > 0 && $plan['days'] <= 7,
            ]);
        }
    }

    protected function seedMedicalCertificates(): void
    {
        $patients = Patient::take(3)->get();
        $doctors = Doctor::where('status', 1)->take(2)->get();
        if ($patients->isEmpty() || $doctors->isEmpty()) {
            return;
        }

        $certificates = [
            [
                'type' => 'sick_leave',
                'title' => 'إجازة مرضية',
                'content' => "بناءً على الفحص الطبي، يُفاد بأن المريض/ة يعاني من حالة صحية تستدعي الراحة.\n\nيُمنح إجازة مرضية للفترة المحددة أدناه.",
                'days_off' => 5,
                'valid_from' => now()->toDateString(),
                'valid_until' => now()->addDays(5)->toDateString(),
            ],
            [
                'type' => 'fitness',
                'title' => 'شهادة لياقة طبية',
                'content' => "بعد الفحص السريري والتحاليل اللازمة، تبين أن المريض/ة لائق/ة طبياً ومؤهل/ة للعمل.\n\nلا مانع من مزاولة النشاط المطلوب.",
                'days_off' => null,
                'valid_from' => now()->toDateString(),
                'valid_until' => now()->addYear()->toDateString(),
            ],
            [
                'type' => 'medical_report',
                'title' => 'تقرير طبي للجهات الرسمية',
                'content' => "تقرير طبي مفصل:\n\n- التشخيص: حالة مستقرة تحت المتابعة.\n- التوصيات: متابعة دورية كل 3 أشهر.\n- العلاج: حسب الوصفة المرفقة.",
                'days_off' => null,
                'valid_from' => now()->subDays(2)->toDateString(),
                'valid_until' => now()->addMonths(3)->toDateString(),
            ],
        ];

        foreach ($certificates as $index => $data) {
            MedicalCertificate::create(array_merge($data, [
                'patient_id' => $patients[$index % $patients->count()]->id,
                'doctor_id' => $doctors[$index % $doctors->count()]->id,
                'reference_number' => MedicalCertificate::generateReference(),
                'issued_at' => now()->subDays($index),
            ]));
        }
    }

    protected function seedAmbulanceWithTriageAndTimeline(): void
    {
        $ambulance1 = Ambulance::where('car_number', 'AMB-101')->first();
        $ambulance2 = Ambulance::where('car_number', 'AMB-202')->first();
        $gastroSection = Section::whereTranslationLike('name', '%باطنة%')->first() ?? Section::first();
        $gastroDoctor = Doctor::where('section_id', optional($gastroSection)->id)->where('status', 1)->first();
        $patient = Patient::first();
        $emergencyAppointment = Appointment::where('is_emergency', true)->first();

        // 1) حرج — قيد الانتظار
        $critical = AmbulanceRequest::create([
            'patient_name' => 'خالد عبد الرحمن',
            'phone' => '0593555001',
            'address' => 'دمشق — المزة — مقابل حديقة الجلاء',
            'notes' => 'ألم صدر حاد — يشتبه بجلطة',
            'triage_level' => 'critical',
            'status' => 'pending',
            'requested_at' => now()->subMinutes(8),
            'patient_id' => optional($patient)->id,
        ]);
        AmbulanceWorkflowService::initialTimeline($critical);

        // 2) عاجل — في الطريق
        $urgent = AmbulanceRequest::create([
            'patient_name' => 'سمير جمعه',
            'phone' => '0593555002',
            'address' => 'دمشق — كفر سوسة — بناء 12',
            'notes' => 'سقوط — إصابة في الركبة',
            'triage_level' => 'urgent',
            'ambulance_id' => optional($ambulance1)->id,
            'status' => 'en_route',
            'requested_at' => now()->subMinutes(35),
        ]);
        $this->seedTimeline($urgent, [
            ['pending', 'تم استلام الطلب', 35],
            ['dispatched', 'تم إرسال سيارة AMB-101', 30],
            ['en_route', 'السيارة في الطريق', 10],
        ]);

        // 3) عادي — مكتمل
        $normal = AmbulanceRequest::create([
            'patient_name' => 'نبيل حداد',
            'phone' => '0593555003',
            'address' => 'ريف دمشق — بلدة جديدة',
            'notes' => 'نقل مجدول — مريض كبار سن',
            'triage_level' => 'normal',
            'ambulance_id' => optional($ambulance2)->id,
            'status' => 'completed',
            'requested_at' => now()->subHours(4),
        ]);
        $this->seedTimeline($normal, [
            ['pending', 'تم استلام الطلب', 240],
            ['dispatched', 'تم الإرسال', 230],
            ['en_route', 'في الطريق', 200],
            ['arrived', 'وصلت السيارة', 180],
            ['transported', 'تم نقل المريض', 170],
            ['completed', 'اكتمل الطلب', 160],
        ]);

        // 4) حرج — محوّل لعيادة
        $transferred = AmbulanceRequest::create([
            'patient_name' => 'لينا سعيد',
            'phone' => '0593555004',
            'address' => 'دمشق — الشaghour',
            'notes' => 'ضيق تنفس — تم التحويل للعيادة',
            'triage_level' => 'critical',
            'ambulance_id' => optional($ambulance1)->id,
            'status' => 'transported',
            'section_id' => optional($gastroSection)->id,
            'doctor_id' => optional($gastroDoctor)->id,
            'appointment_id' => optional($emergencyAppointment)->id,
            'transferred_to_clinic' => true,
            'transfer_notes' => 'تحويل عاجل لعيادة باطنة — ضيق تنفس',
            'requested_at' => now()->subHours(2),
            'patient_id' => Patient::skip(1)->first()?->id,
        ]);
        $this->seedTimeline($transferred, [
            ['pending', 'طلب إسعاف حرج', 120],
            ['dispatched', 'إرسال فوري', 115],
            ['en_route', 'في الطريق', 100],
            ['arrived', 'وصلت', 85],
            ['transported', 'تم التحويل لعيادة الباطنة', 80],
        ]);

        // 5) ملغى
        $cancelled = AmbulanceRequest::create([
            'patient_name' => 'أحمد فاروق',
            'phone' => '0593555005',
            'address' => 'دمشق — الميدان',
            'notes' => 'تم الإلغاء — تحسنت الحالة',
            'triage_level' => 'normal',
            'status' => 'cancelled',
            'requested_at' => now()->subHours(6),
        ]);
        $this->seedTimeline($cancelled, [
            ['pending', 'تم الاستلام', 360],
            ['cancelled', 'ألغى المتصل الطلب', 350],
        ]);
    }

    protected function seedTimeline(AmbulanceRequest $request, array $steps): void
    {
        foreach ($steps as [$status, $notes, $minutesAgo]) {
            AmbulanceRequestTimeline::create([
                'ambulance_request_id' => $request->id,
                'status' => $status,
                'notes' => $notes,
                'recorded_at' => now()->subMinutes($minutesAgo),
            ]);
        }
    }

    protected function seedMedicinesAndDispensings(): void
    {
        // إعادة ضبط حالة الوصفات لعرض دورة كشف → وصفة → صرف
        DB::table('prescriptions')->update([
            'is_dispensed' => false,
            'dispensed_at' => null,
            'medicine_id' => null,
        ]);

        $medicines = [
            ['name' => 'باراسيتامول 500', 'generic_name' => 'Paracetamol', 'quantity' => 200, 'unit_price' => 2.50, 'expiry_date' => now()->addYear(), 'min_stock_level' => 30],
            ['name' => 'أموكسيسيلين 500', 'generic_name' => 'Amoxicillin', 'quantity' => 80, 'unit_price' => 8.00, 'expiry_date' => now()->addMonths(8), 'min_stock_level' => 20],
            ['name' => 'أوميبرازول 20', 'generic_name' => 'Omeprazole', 'quantity' => 15, 'unit_price' => 12.00, 'expiry_date' => now()->addMonths(6), 'min_stock_level' => 25],
            ['name' => 'ميتفورمين 500', 'generic_name' => 'Metformin', 'quantity' => 120, 'unit_price' => 5.50, 'expiry_date' => now()->addMonths(10), 'min_stock_level' => 20],
            ['name' => 'أملوديبين 5', 'generic_name' => 'Amlodipine', 'quantity' => 8, 'unit_price' => 7.00, 'expiry_date' => now()->addMonths(5), 'min_stock_level' => 15],
            ['name' => 'فيتامين D3', 'generic_name' => 'Cholecalciferol', 'quantity' => 50, 'unit_price' => 15.00, 'expiry_date' => now()->addMonths(14), 'min_stock_level' => 10],
        ];

        foreach ($medicines as $data) {
            Medicine::create(array_merge($data, ['is_active' => true]));
        }

        $diagnostic = Diagnostic::with(['prescriptions', 'patient', 'Doctor'])
            ->whereHas('prescriptions')
            ->first();

        if (!$diagnostic) {
            return;
        }

        $paracetamol = Medicine::where('name', 'like', '%باراسيتامول%')->first();
        $amoxicillin = Medicine::where('name', 'like', '%أموكسيسيلين%')->first();
        $prescriptions = $diagnostic->prescriptions->take(2);

        if ($prescriptions->isEmpty() || !$paracetamol) {
            return;
        }

        $invoice = PharmacyInvoice::create([
            'invoice_number' => PharmacyInvoice::generateNumber(),
            'patient_id' => $diagnostic->patient_id,
            'diagnostic_id' => $diagnostic->id,
            'doctor_id' => $diagnostic->doctor_id,
            'dispensed_by' => 1,
            'dispensed_by_type' => 'admin',
            'notes' => 'صرف تجريبي — إغلاق دورة كشف → وصفة → صرف',
            'issued_at' => now()->subDays(2),
        ]);

        $subtotal = 0;

        foreach ($prescriptions as $index => $prescription) {
            $medicine = $index === 0 ? $paracetamol : ($amoxicillin ?? $paracetamol);
            $qty = 2;
            $lineTotal = $medicine->unit_price * $qty;

            PharmacyDispensing::create([
                'pharmacy_invoice_id' => $invoice->id,
                'patient_id' => $diagnostic->patient_id,
                'diagnostic_id' => $diagnostic->id,
                'prescription_id' => $prescription->id,
                'medicine_id' => $medicine->id,
                'quantity_dispensed' => $qty,
                'unit_price' => $medicine->unit_price,
                'total_price' => $lineTotal,
                'dispensed_by' => 1,
                'dispensed_by_type' => 'admin',
                'dispensed_at' => now()->subDays(2),
            ]);

            $medicine->decrement('quantity', $qty);

            $prescription->update([
                'is_dispensed' => true,
                'dispensed_at' => now()->subDays(2),
                'medicine_id' => $medicine->id,
            ]);

            $subtotal += $lineTotal;
        }

        $invoice->update([
            'subtotal' => $subtotal,
            'total_amount' => $subtotal,
        ]);
    }

    protected function seedPatientPackageUsages(): void
    {
        $patient = Patient::first();
        $group = Group::where('is_health_package', true)->with('service_group')->first();

        if (!$patient || !$group || $group->service_group->isEmpty()) {
            return;
        }

        foreach ($group->service_group as $index => $service) {
            PatientPackageUsage::create([
                'patient_id' => $patient->id,
                'group_id' => $group->id,
                'service_id' => $service->id,
                'quantity_allowed' => $service->pivot->quantity ?? 1,
                'quantity_used' => $index === 0 ? 1 : 0,
                'purchased_at' => now()->subDays(10),
                'expires_at' => now()->addDays($group->validity_days ?? 90),
            ]);
        }

        $patient2 = Patient::skip(1)->first();
        $group2 = Group::where('is_health_package', true)->skip(1)->first();
        if ($patient2 && $group2) {
            foreach ($group2->service_group as $service) {
                PatientPackageUsage::create([
                    'patient_id' => $patient2->id,
                    'group_id' => $group2->id,
                    'service_id' => $service->id,
                    'quantity_allowed' => $service->pivot->quantity ?? 1,
                    'quantity_used' => 0,
                    'purchased_at' => now()->subDays(3),
                    'expires_at' => now()->addDays($group2->validity_days ?? 60),
                ]);
            }
        }
    }

    protected function seedExternalRecords(): void
    {
        $patients = Patient::take(3)->get();
        if ($patients->isEmpty()) {
            return;
        }

        Storage::disk('public')->makeDirectory('external-records/demo');

        $records = [
            ['title' => 'تحليل دم — مارس 2025', 'type' => 'lab', 'content' => 'نتائج تحليل دم شامل — Hb: 14.2'],
            ['title' => 'أشعة صدر — 2024', 'type' => 'ray', 'content' => 'أشعة سينية للصدر — لا تجمع رئوي'],
            ['title' => 'تقرير مستشفى سابق', 'type' => 'report', 'content' => 'تقرير خروج — متابعة ضغط'],
        ];

        foreach ($records as $index => $data) {
            $path = 'external-records/demo/patient-' . $patients[$index % $patients->count()]->id . '-' . ($index + 1) . '.txt';
            Storage::disk('public')->put($path, $data['content']);

            ExternalRecord::create([
                'patient_id' => $patients[$index % $patients->count()]->id,
                'title' => $data['title'],
                'type' => $data['type'],
                'file_path' => $path,
                'notes' => 'ملف تجريبي للعرض',
            ]);
        }
    }

    protected function seedShamCashDemo(): void
    {
        $setting = SiteSetting::current();
        Storage::disk('public')->makeDirectory('sham-cash');

        $qrPath = 'sham-cash/demo-qr-placeholder.txt';
        if (!Storage::disk('public')->exists($qrPath)) {
            Storage::disk('public')->put($qrPath, 'Demo QR placeholder — replace with real QR image in admin settings.');
        }

        $setting->update([
            'sham_cash_enabled' => true,
            'sham_cash_wallet' => '0999123456',
            'sham_cash_instructions' => "1. افتح تطبيق شام كاش\n2. امسح QR أو انسخ عنوان المحفظة\n3. ادفع المبلغ بالضبط\n4. ارفع screenshot الإيصال",
        ]);

        $patient = Patient::first();
        $invoices = Invoice::take(4)->get();
        if ($invoices->isEmpty() || !$patient) {
            return;
        }

        // فاتورة بانتظار مراجعة الدفع
        $pendingInvoice = $invoices[0];
        $pendingInvoice->update(['payment_status' => 'pending_review']);
        $receiptPath = 'sham-cash/receipts/demo-receipt-pending.txt';
        Storage::disk('public')->put($receiptPath, 'Demo payment receipt — pending review');
        ShamCashPayment::create([
            'invoice_id' => $pendingInvoice->id,
            'patient_id' => $pendingInvoice->patient_id,
            'amount' => $pendingInvoice->total_with_tax,
            'status' => 'pending_review',
            'receipt_path' => $receiptPath,
            'transaction_reference' => 'SC-DEMO-001',
            'patient_notes' => 'دفعت عبر شام كاش — يرجى المراجعة',
        ]);

        // فاتورة مدفوعة (معتمدة)
        if ($invoices->count() > 1) {
            $paidInvoice = $invoices[1];
            $paidInvoice->update(['payment_status' => 'paid']);
            ShamCashPayment::create([
                'invoice_id' => $paidInvoice->id,
                'patient_id' => $paidInvoice->patient_id,
                'amount' => $paidInvoice->total_with_tax,
                'status' => 'approved',
                'receipt_path' => 'sham-cash/receipts/demo-receipt-approved.txt',
                'transaction_reference' => 'SC-DEMO-002',
                'reviewed_by' => 1,
                'reviewed_at' => now()->subDay(),
            ]);
            Storage::disk('public')->put('sham-cash/receipts/demo-receipt-approved.txt', 'Approved demo receipt');
        }

        // فاتورة مرفوضة
        if ($invoices->count() > 2) {
            $rejectedInvoice = $invoices[2];
            $rejectedInvoice->update(['payment_status' => 'rejected']);
            ShamCashPayment::create([
                'invoice_id' => $rejectedInvoice->id,
                'patient_id' => $rejectedInvoice->patient_id,
                'amount' => $rejectedInvoice->total_with_tax,
                'status' => 'rejected',
                'receipt_path' => 'sham-cash/receipts/demo-receipt-rejected.txt',
                'transaction_reference' => 'SC-DEMO-003',
                'admin_notes' => 'المبلغ غير مطابق — يرجى إعادة الدفع',
                'reviewed_by' => 1,
                'reviewed_at' => now()->subHours(5),
            ]);
            Storage::disk('public')->put('sham-cash/receipts/demo-receipt-rejected.txt', 'Rejected demo receipt');
        }

        // فاتورة غير مدفوعة (للتجربة)
        if ($invoices->count() > 3) {
            $invoices[3]->update(['payment_status' => 'unpaid']);
        }
    }

    protected function seedActivityLogs(): void
    {
        $logs = [
            ['action' => 'referral_created', 'user_type' => 'doctor', 'user_id' => 1],
            ['action' => 'referral_accepted', 'user_type' => 'doctor', 'user_id' => 2],
            ['action' => 'ambulance_dispatched', 'user_type' => 'admin', 'user_id' => 1],
            ['action' => 'ambulance_clinic_transfer', 'user_type' => 'admin', 'user_id' => 1],
            ['action' => 'follow_up_created', 'user_type' => 'doctor', 'user_id' => 1],
            ['action' => 'certificate_issued', 'user_type' => 'doctor', 'user_id' => 1],
            ['action' => 'pharmacy_dispensed', 'user_type' => 'admin', 'user_id' => 1],
            ['action' => 'medicine_created', 'user_type' => 'admin', 'user_id' => 1],
            ['action' => 'external_record_uploaded', 'user_type' => 'patient', 'user_id' => 1],
            ['action' => 'sham_cash_payment_submitted', 'user_type' => 'patient', 'user_id' => 1],
            ['action' => 'sham_cash_payment_approved', 'user_type' => 'admin', 'user_id' => 1],
        ];

        foreach ($logs as $index => $data) {
            ActivityLog::create([
                'user_id' => $data['user_id'],
                'user_type' => $data['user_type'],
                'action' => $data['action'],
                'model_type' => null,
                'model_id' => null,
                'ip_address' => '127.0.0.1',
                'created_at' => now()->subHours($index * 3),
                'updated_at' => now()->subHours($index * 3),
            ]);
        }
    }
}
