<?php

namespace Database\Seeders;

use App\Models\Ambulance;
use App\Models\Insurance;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AmbulanceInsuranceSeeder extends Seeder
{
    public function run()
    {
        DB::table('ambulance_translations')->delete();
        DB::table('ambulances')->delete();
        DB::table('insurance_translations')->delete();
        DB::table('insurances')->delete();

        $ambulances = [
            [
                'car_number' => 'AMB-101',
                'car_model' => 'Mercedes Sprinter',
                'car_year_made' => '2020',
                'driver_license_number' => 'DL-9001',
                'driver_phone' => '0594000001',
                'is_available' => 1,
                'car_type' => 1,
                'driver_name' => 'سائق أحمد',
                'paramedic_name' => 'مسعف خالد',
                'coverage_area' => 'دمشق — المزة',
                'last_maintenance_date' => now()->subMonths(2)->toDateString(),
                'notes' => 'سيارة إسعاف مجهزة بالكامل',
            ],
            [
                'car_number' => 'AMB-202',
                'car_model' => 'Toyota Hiace',
                'car_year_made' => '2019',
                'driver_license_number' => 'DL-9002',
                'driver_phone' => '0594000002',
                'is_available' => 1,
                'car_type' => 1,
                'driver_name' => 'سائق محمود',
                'paramedic_name' => 'مسعفة سمر',
                'coverage_area' => 'دمشق — كفر سوسة',
                'last_maintenance_date' => now()->subMonths(4)->toDateString(),
                'notes' => 'جاهزة للطوارئ',
            ],
            [
                'car_number' => 'AMB-303',
                'car_model' => 'Ford Transit',
                'car_year_made' => '2018',
                'driver_license_number' => 'DL-9003',
                'driver_phone' => '0594000003',
                'is_available' => 0,
                'car_type' => 2,
                'driver_name' => 'سائق سامر',
                'paramedic_name' => 'مسعف نادر',
                'coverage_area' => 'ريف دمشق',
                'last_maintenance_date' => now()->subMonth()->toDateString(),
                'notes' => 'تحت الصيانة',
            ],
        ];

        foreach ($ambulances as $data) {
            $ambulance = new Ambulance();
            $ambulance->car_number = $data['car_number'];
            $ambulance->car_model = $data['car_model'];
            $ambulance->car_year_made = $data['car_year_made'];
            $ambulance->driver_license_number = $data['driver_license_number'];
            $ambulance->driver_phone = $data['driver_phone'];
            $ambulance->is_available = $data['is_available'];
            $ambulance->car_type = $data['car_type'];
            $ambulance->paramedic_name = $data['paramedic_name'] ?? null;
            $ambulance->coverage_area = $data['coverage_area'] ?? null;
            $ambulance->last_maintenance_date = $data['last_maintenance_date'] ?? null;
            $ambulance->save();

            $ambulance->driver_name = $data['driver_name'];
            $ambulance->notes = $data['notes'];
            $ambulance->save();
        }

        $insurances = [
            [
                'insurance_code' => 'INS-001',
                'discount_percentage' => '20',
                'Company_rate' => '80',
                'status' => 1,
                'name' => 'شركة التأمين الوطنية',
                'notes' => 'تغطية أساسية للعيادات',
            ],
            [
                'insurance_code' => 'INS-002',
                'discount_percentage' => '15',
                'Company_rate' => '85',
                'status' => 1,
                'name' => 'تأمين الرعاية الصحية',
                'notes' => 'تغطية متوسطة تشمل الأشعة',
            ],
            [
                'insurance_code' => 'INS-003',
                'discount_percentage' => '10',
                'Company_rate' => '90',
                'status' => 1,
                'name' => 'تأمين الحياة العربي',
                'notes' => 'تغطية شاملة للعمليات',
            ],
        ];

        foreach ($insurances as $data) {
            $insurance = new Insurance();
            $insurance->insurance_code = $data['insurance_code'];
            $insurance->discount_percentage = $data['discount_percentage'];
            $insurance->Company_rate = $data['Company_rate'];
            $insurance->status = $data['status'];
            $insurance->save();

            $insurance->name = $data['name'];
            $insurance->notes = $data['notes'];
            $insurance->save();
        }
    }
}
