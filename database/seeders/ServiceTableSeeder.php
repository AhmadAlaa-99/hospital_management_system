<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ServiceTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('Service_translations')->delete();
        DB::table('Services')->delete();

        $services = [
            ['name' => 'كشف', 'price' => 50, 'description' => 'كشف طبي أولي'],
            ['name' => 'إعادة كشف', 'price' => 30, 'description' => 'متابعة بعد الكشف'],
            ['name' => 'تخطيط قلب', 'price' => 80, 'description' => 'فحص تخطيط كهربائية القلب'],
            ['name' => 'أشعة سينية', 'price' => 100, 'description' => 'تصوير بالأشعة السينية'],
            ['name' => 'تحليل دم شامل', 'price' => 70, 'description' => 'فحوصات مخبرية للدم'],
            ['name' => 'موجات فوق صوتية', 'price' => 120, 'description' => 'فحص بالموجات فوق الصوتية'],
            ['name' => 'منظار جهاز هضمي', 'price' => 250, 'description' => 'منظار تشخيصي للجهاز الهضمي'],
            ['name' => 'فحص نظر شامل', 'price' => 60, 'description' => 'فحص كامل للعينين'],
        ];

        foreach ($services as $data) {
            $service = new Service();
            $service->price = $data['price'];
            $service->description = $data['description'];
            $service->status = 1;
            $service->save();

            $service->name = $data['name'];
            $service->save();
        }
    }
}
