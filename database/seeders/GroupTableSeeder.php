<?php

namespace Database\Seeders;

use App\Models\Group;
use App\Models\Service;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GroupTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('Service_Group')->delete();
        DB::table('group_translations')->delete();
        DB::table('groups')->delete();

        $services = Service::all();
        if ($services->count() < 3) {
            return;
        }

        $packages = [
            [
                'name' => 'باقة فحص شامل',
                'notes' => 'باقة مناسبة للفحوصات الدورية',
                'discount_value' => 40,
                'tax_rate' => '17',
                'service_names' => ['كشف', 'تحليل دم شامل', 'أشعة سينية'],
            ],
            [
                'name' => 'باقة صحة القلب',
                'notes' => 'فحوصات متخصصة للقلب',
                'discount_value' => 25,
                'tax_rate' => '17',
                'service_names' => ['كشف', 'تخطيط قلب'],
            ],
            [
                'name' => 'باقة الجهاز الهضمي',
                'notes' => 'تشخيص أمراض الجهاز الهضمي',
                'discount_value' => 50,
                'tax_rate' => '17',
                'service_names' => ['كشف', 'موجات فوق صوتية', 'منظار جهاز هضمي'],
            ],
        ];

        foreach ($packages as $package) {
            $selected = $services->filter(function ($service) use ($package) {
                return in_array($service->name, $package['service_names'], true);
            });

            if ($selected->isEmpty()) {
                continue;
            }

            $totalBefore = $selected->sum('price');
            $totalAfter = max($totalBefore - $package['discount_value'], 0);
            $taxValue = $totalAfter * ((float) $package['tax_rate'] / 100);
            $totalWithTax = $totalAfter + $taxValue;

            $group = new Group();
            $group->Total_before_discount = $totalBefore;
            $group->discount_value = $package['discount_value'];
            $group->Total_after_discount = $totalAfter;
            $group->tax_rate = $package['tax_rate'];
            $group->Total_with_tax = $totalWithTax;
            $group->save();

            $group->name = $package['name'];
            $group->notes = $package['notes'];
            $group->save();

            foreach ($selected as $service) {
                DB::table('Service_Group')->insert([
                    'Group_id' => $group->id,
                    'Service_id' => $service->id,
                    'quantity' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
