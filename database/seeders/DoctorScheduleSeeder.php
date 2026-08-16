<?php

namespace Database\Seeders;

use App\Models\Doctor;
use App\Models\DoctorSchedule;
use Illuminate\Database\Seeder;

class DoctorScheduleSeeder extends Seeder
{
    public function run()
    {
        // أيام العمل: أحد–خميس (0–4) + سبت (6) ليتوافق مع دوام المستشفى
        $days = [0, 1, 2, 3, 4, 6];

        foreach (Doctor::all() as $doctor) {
            foreach ($days as $day) {
                DoctorSchedule::updateOrCreate(
                    ['doctor_id' => $doctor->id, 'day_of_week' => $day],
                    [
                        'start_time' => '09:00:00',
                        'end_time' => '17:00:00',
                        'slot_duration' => 30,
                    ]
                );
            }
        }
    }
}
