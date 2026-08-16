<?php

namespace Database\Seeders;

use App\Models\Appointment;
use App\Models\DoctorRating;
use App\Models\Patient;
use Illuminate\Database\Seeder;

class DoctorRatingSeeder extends Seeder
{
    public function run(): void
    {
        $finished = Appointment::where('type', 'منتهي')->get();
        $patients = Patient::all();

        if ($finished->isEmpty() || $patients->isEmpty()) {
            $this->command?->warn('DoctorRatingSeeder: no finished appointments or patients found.');
            return;
        }

        $comments = [
            'طبيب ممتاز وشرح الحالة بوضوح.',
            'جيد لكن لم يقس الضغط.',
            'خدمة جيدة — أنصح بالزيارة.',
            'تعامل راقٍ وانتظار قصير.',
        ];

        foreach ($finished->take(4) as $index => $appointment) {
            DoctorRating::updateOrCreate(
                ['appointment_id' => $appointment->id],
                [
                    'doctor_id' => $appointment->doctor_id,
                    'patient_id' => $patients[$index % $patients->count()]->id,
                    'rating' => [5, 3, 5, 4][$index % 4],
                    'comment' => $comments[$index % count($comments)],
                    'share_on_homepage' => true,
                    'homepage_status' => DoctorRating::HOMEPAGE_APPROVED,
                ]
            );
        }
    }
}
