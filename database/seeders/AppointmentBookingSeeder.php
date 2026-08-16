<?php

namespace Database\Seeders;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Section;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AppointmentBookingSeeder extends Seeder
{
    public function run()
    {
        DB::table('appointments')->delete();

        $doctors = Doctor::with('section')->get();
        if ($doctors->isEmpty()) {
            return;
        }

        $bookings = [
            ['name' => 'عبدالله منصور', 'email' => 'abdullah@demo.com', 'phone' => '0593000001', 'type' => 'غير مؤكد', 'notes' => 'ألم في الصدر', 'days' => null],
            ['name' => 'هدى سالم', 'email' => 'huda@demo.com', 'phone' => '0593000002', 'type' => 'غير مؤكد', 'notes' => 'صداع مستمر', 'days' => null],
            ['name' => 'كريم يوسف', 'email' => 'kareem@demo.com', 'phone' => '0593000003', 'type' => 'غير مؤكد', 'notes' => 'متابعة ضغط', 'days' => null],
            ['name' => 'سلمى فواز', 'email' => 'salma@demo.com', 'phone' => '0593000004', 'type' => 'مؤكد', 'notes' => 'فحص نظر', 'days' => 2],
            ['name' => 'ماجد حسين', 'email' => 'majed@demo.com', 'phone' => '0593000005', 'type' => 'مؤكد', 'notes' => 'ألم بطن', 'days' => 3],
            ['name' => 'إيناس جمال', 'email' => 'enas@demo.com', 'phone' => '0593000006', 'type' => 'مؤكد', 'notes' => 'متابعة طفل', 'days' => 1],
            ['name' => 'فادي ناصر', 'email' => 'fadi@demo.com', 'phone' => '0593000007', 'type' => 'مؤكد', 'notes' => 'استشارة جراحة', 'days' => 4],
            ['name' => 'رانية عارف', 'email' => 'rania@demo.com', 'phone' => '0593000008', 'type' => 'منتهي', 'notes' => 'تمت الزيارة', 'days' => -3],
            ['name' => 'باسم خضر', 'email' => 'basem@demo.com', 'phone' => '0593000009', 'type' => 'منتهي', 'notes' => 'تمت المتابعة', 'days' => -5],
            ['name' => 'جنى وليد', 'email' => 'jana@demo.com', 'phone' => '0593000010', 'type' => 'منتهي', 'notes' => 'انتهى الموعد', 'days' => -2],
            ['name' => 'أنس طارق', 'email' => 'anas@demo.com', 'phone' => '0593000011', 'type' => 'غير مؤكد', 'notes' => 'حساسية موسمية', 'days' => null],
            ['name' => 'ميساء روحي', 'email' => 'maysa@demo.com', 'phone' => '0593000012', 'type' => 'مؤكد', 'notes' => 'تورم مفاصل', 'days' => 5],
        ];

        foreach ($bookings as $index => $data) {
            $doctor = $doctors[$index % $doctors->count()];

            Appointment::create([
                'doctor_id' => $doctor->id,
                'section_id' => $doctor->section_id,
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'type' => $data['type'],
                'appointment' => $data['days'] === null ? null : now()->addDays($data['days'])->setTime(10 + ($index % 6), 0),
                'notes' => $data['notes'],
            ]);

            if ($data['type'] === 'غير مؤكد') {
                $notification = new \App\Models\Notification();
                $notification->reader_status = 0;
                $notification->user_id = $doctor->id;
                $notification->message = 'حجز موعد جديد من المريض: ' . $data['name'];
                $notification->save();
            }
        }

        $admin = \App\Models\Admin::query()->first();
        if ($admin) {
            $pendingCount = Appointment::where('type', 'غير مؤكد')->count();
            $adminNotes = [
                'يوجد ' . $pendingCount . ' مواعيد بانتظار التأكيد',
                'تم تحديث بيانات النظام التجريبية بنجاح',
                'راجع قائمة المواعيد الجديدة من لوحة التحكم',
            ];
            foreach ($adminNotes as $message) {
                $notification = new \App\Models\Notification();
                $notification->reader_status = 0;
                $notification->user_id = $admin->id;
                $notification->message = $message;
                $notification->save();
            }
        }
    }
}
