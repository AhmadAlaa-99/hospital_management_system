<?php

namespace Database\Seeders;

use App\Models\RayEmployee;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class RayEmployeeTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('ray_employees')->delete();

        $employees = [
            ['name' => 'محمد الأشعة', 'email' => 'm@yahoo.com'],
            ['name' => 'أحمد فني الأشعة', 'email' => 'ray@hospital.com'],
            ['name' => 'سعيد التصوير', 'email' => 'saeed.ray@hospital.com'],
        ];

        foreach ($employees as $data) {
            $employee = new RayEmployee();
            $employee->name = $data['name'];
            $employee->email = $data['email'];
            $employee->password = Hash::make('12345678');
            $employee->save();
        }
    }
}
