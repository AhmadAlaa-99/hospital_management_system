<?php

namespace Database\Seeders;

use App\Models\LaboratorieEmployee;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class LaboratorieEmployeeTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('laboratorie_employees')->delete();

        $employees = [
            ['name' => 'محمود المختبر', 'email' => 'lab@hospital.com'],
            ['name' => 'سمية التحاليل', 'email' => 'somaya.lab@hospital.com'],
            ['name' => 'فادي المختبر', 'email' => 'fadi.lab@hospital.com'],
        ];

        foreach ($employees as $data) {
            $employee = new LaboratorieEmployee();
            $employee->name = $data['name'];
            $employee->email = $data['email'];
            $employee->password = Hash::make('12345678');
            $employee->save();
        }
    }
}
