<?php

namespace Database\Seeders;

use App\Models\Doctor;
use App\Models\Image;
use App\Models\Section;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DoctorTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('images')->where('imageable_type', 'App\\Models\\Doctor')->delete();
        DB::table('doctor_translations')->delete();
        DB::table('doctors')->delete();

        $sections = Section::all();
        if ($sections->isEmpty()) {
            return;
        }

        $password = Hash::make('12345678');
        $images = [
            'doctor-1.jpg', 'doctor-2.png', 'doctor-3.jpg', 'doctor-4.jpg',
            'doctor-5.png', 'team-1.jpg', 'team-2.jpg', 'team-3.jpg',
            'team-4.jpg', 'face-1.jpg', 'face-2.jpg', 'face-3.jpg',
            'face-4.jpg', 'face-5.jpg', 'face-6.jpg', 'ahmd-hmdy.png',
            'smyr-gmal-alsyd.png',
        ];

        $doctors = [
            ['name' => 'د. أحمد محمود', 'email' => 'ahmed.cardio@hospital.com', 'phone' => '0591000001', 'section' => 'قسم القلب والأوعية الدموية'],
            ['name' => 'د. سارة الخطيب', 'email' => 'sara.cardio@hospital.com', 'phone' => '0591000002', 'section' => 'قسم القلب والأوعية الدموية'],
            ['name' => 'د. خالد ناصر', 'email' => 'khaled.neuro@hospital.com', 'phone' => '0591000003', 'section' => 'قسم المخ والأعصاب'],
            ['name' => 'د. نور الهدى', 'email' => 'noor.neuro@hospital.com', 'phone' => '0591000004', 'section' => 'قسم المخ والأعصاب'],
            ['name' => 'د. يوسف علي', 'email' => 'yousef.pedia@hospital.com', 'phone' => '0591000005', 'section' => 'قسم الأطفال'],
            ['name' => 'د. مريم حسن', 'email' => 'mariam.pedia@hospital.com', 'phone' => '0591000006', 'section' => 'قسم الأطفال'],
            ['name' => 'د. عمر فادي', 'email' => 'omar.eye@hospital.com', 'phone' => '0591000007', 'section' => 'قسم العيون'],
            ['name' => 'د. لينا سمير', 'email' => 'lina.eye@hospital.com', 'phone' => '0591000008', 'section' => 'قسم العيون'],
            ['name' => 'د. رامي جبر', 'email' => 'rami.surgery@hospital.com', 'phone' => '0591000009', 'section' => 'قسم الجراحة العامة'],
            ['name' => 'د. هبة عادل', 'email' => 'hiba.surgery@hospital.com', 'phone' => '0591000010', 'section' => 'قسم الجراحة العامة'],
            ['name' => 'د. طارق سعيد', 'email' => 'tareq.gastro@hospital.com', 'phone' => '0591000011', 'section' => 'قسم الباطنة والجهاز الهضمي'],
            ['name' => 'د. دانا كريم', 'email' => 'dana.gastro@hospital.com', 'phone' => '0591000012', 'section' => 'قسم الباطنة والجهاز الهضمي'],
            // حساب تجريبي سريع للدخول
            ['name' => 'د. محمد الطبيب', 'email' => 'doctor@gmail.com', 'phone' => '0591000099', 'section' => 'قسم القلب والأوعية الدموية'],
        ];

        foreach ($doctors as $index => $data) {
            $section = $sections->first(function ($item) use ($data) {
                return optional($item->translate('ar'))->name === $data['section'];
            }) ?? $sections->first();

            $doctor = new Doctor();
            $doctor->email = $data['email'];
            $doctor->email_verified_at = now();
            $doctor->password = $password;
            $doctor->phone = $data['phone'];
            $doctor->section_id = $section->id;
            $doctor->status = 1;
            $doctor->save();

            $doctor->translateOrNew('ar')->name = $data['name'];
            $doctor->translateOrNew('en')->name = $data['name'];
            $doctor->save();

            $filename = $images[$index % count($images)];
            $image = new Image();
            $image->filename = $filename;
            $image->imageable_id = $doctor->id;
            $image->imageable_type = 'App\Models\Doctor';
            $image->save();
        }
    }
}
