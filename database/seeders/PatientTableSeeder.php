<?php

namespace Database\Seeders;

use App\Models\Patient;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class PatientTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('patient_translations')->delete();
        DB::table('patients')->delete();

        $password = Hash::make('12345678');

        $patients = [
            [
                'email' => 'patient@yahoo.com',
                'name' => 'محمد السيد',
                'Address' => 'غزة - الرمال',
                'Date_Birth' => '1988-12-01',
                'Phone' => '0592000001',
                'Gender' => '1',
                'Blood_Group' => 'A+',
            ],
            [
                'email' => 'fatima.ali@demo.com',
                'name' => 'فاطمة علي',
                'Address' => 'غزة - الشجاعية',
                'Date_Birth' => '1992-05-14',
                'Phone' => '0592000002',
                'Gender' => '2',
                'Blood_Group' => 'O+',
            ],
            [
                'email' => 'hassan.omar@demo.com',
                'name' => 'حسن عمر',
                'Address' => 'خانيونس - البلد',
                'Date_Birth' => '1979-08-22',
                'Phone' => '0592000003',
                'Gender' => '1',
                'Blood_Group' => 'B+',
            ],
            [
                'email' => 'rana.nabil@demo.com',
                'name' => 'رانا نبيل',
                'Address' => 'رفح - حي الجنينة',
                'Date_Birth' => '1995-03-09',
                'Phone' => '0592000004',
                'Gender' => '2',
                'Blood_Group' => 'A-',
            ],
            [
                'email' => 'sami.khalil@demo.com',
                'name' => 'سامي خليل',
                'Address' => 'دير البلح',
                'Date_Birth' => '1985-11-30',
                'Phone' => '0592000005',
                'Gender' => '1',
                'Blood_Group' => 'AB+',
            ],
            [
                'email' => 'layla.ahmed@demo.com',
                'name' => 'ليلى أحمد',
                'Address' => 'غزة - النصر',
                'Date_Birth' => '2000-01-18',
                'Phone' => '0592000006',
                'Gender' => '2',
                'Blood_Group' => 'O-',
            ],
            [
                'email' => 'ibrahim.saleh@demo.com',
                'name' => 'إبراهيم صالح',
                'Address' => 'جباليا',
                'Date_Birth' => '1970-07-07',
                'Phone' => '0592000007',
                'Gender' => '1',
                'Blood_Group' => 'B-',
            ],
            [
                'email' => 'nada.faisal@demo.com',
                'name' => 'ندى فيصل',
                'Address' => 'غزة - تل الهوا',
                'Date_Birth' => '1998-09-25',
                'Phone' => '0592000008',
                'Gender' => '2',
                'Blood_Group' => 'A+',
            ],
        ];

        foreach ($patients as $data) {
            $patient = new Patient();
            $patient->email = $data['email'];
            $patient->password = $password;
            $patient->Date_Birth = $data['Date_Birth'];
            $patient->Phone = $data['Phone'];
            $patient->Gender = $data['Gender'];
            $patient->Blood_Group = $data['Blood_Group'];
            $patient->save();

            $patient->name = $data['name'];
            $patient->Address = $data['Address'];
            $patient->save();
        }
    }
}
