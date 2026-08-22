<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * Master demo seeder — loads all HMS demo data in correct order.
 *
 * Usage:
 *   php artisan db:seed
 *   php artisan db:seed --class=HmsFullDemoSeeder
 *   php artisan migrate:fresh --seed
 */
class HmsFullDemoSeeder extends Seeder
{
    public function run()
    {
        $this->command?->info('▶ Seeding HMS full demo data...');

        $steps = [
            ['UserTableSeeder', 'مستخدمون أساسيون'],
            ['AdminTableSeeder', 'حساب المدير'],
            ['SectionTableSeeder', 'الأقسام الطبية'],
            ['DoctorTableSeeder', 'الأطباء'],
            ['PatientTableSeeder', 'المرضى'],
            ['RayEmployeeTableSeeder', 'موظفو الأشعة'],
            ['LaboratorieEmployeeTableSeeder', 'موظفو المختبر'],
            ['ServiceTableSeeder', 'الخدمات الفردية'],
            ['GroupTableSeeder', 'الخدمات المجمّعة'],
            ['AmbulanceInsuranceSeeder', 'الإسعاف + شركات التأمين'],
            ['AppointmentBookingSeeder', 'المواعيد (حجز / مؤكد / منتهي)'],
            ['InvoiceDemoSeeder', 'الفواتير + تشخيصات + أشعة + مختبر'],
            ['ExtendedFeaturesSeeder', 'الميزات المتقدمة (وصفات، تأمين، تقييم، إسعاف)'],
            ['ClinicExtendedFeaturesSeeder', 'الميزات الجديدة (تحويل، إسعاف، صيدلية، باقات، API)'],
            ['DoctorRatingSeeder', 'تقييمات المرضى للصفحة الرئيسية'],
            ['BlogSiteSettingSeeder', 'المدونة + إعدادات الموقع'],
            ['QueueTicketSeeder', 'نظام الانتظار — أرقام اليوم'],
        ];

        foreach ($steps as [$seeder, $label]) {
            $this->command?->line('  • ' . $label);
            $this->call('Database\\Seeders\\' . $seeder);
        }

        $this->command?->info('✔ HMS demo seeding completed.');
        $this->command?->line('');
        $this->command?->line('  Admin:   admin@gmail.com / 123456789');
        $this->command?->line('  Patient: patient@yahoo.com / 12345678');
        $this->command?->line('  Doctor:  doctor@gmail.com / 12345678');
        $this->command?->line('  API:     POST /api/patient/login');
        $this->command?->line('  Queue:   /queue/display/section/1');
    }
}
