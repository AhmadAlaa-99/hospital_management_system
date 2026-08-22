<?php

namespace App\Services;

use App\Models\Patient;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class PatientRegistrationService
{
    /**
     * إنشاء حساب مريض مع حفظ الاسم والعنوان بالعربي والإنجليزي ليظهر في لوحة الإدارة.
     */
    public function register(array $data): Patient
    {
        return DB::transaction(function () use ($data) {
            $patient = new Patient();
            $patient->email = $data['email'];
            $patient->password = Hash::make($data['password']);
            $patient->Phone = $data['phone'];
            $patient->Gender = (string) ($data['gender'] ?? '1');
            $patient->Date_Birth = $data['date_birth'] ?? now()->subYears(25)->toDateString();
            $patient->Blood_Group = $data['blood_group'] ?? 'O+';
            $patient->save();

            $name = trim((string) $data['name']);
            $address = trim((string) ($data['address'] ?? 'سوريا'));

            foreach (['ar', 'en'] as $locale) {
                $patient->translateOrNew($locale)->name = $name;
                $patient->translateOrNew($locale)->Address = $address;
            }

            $patient->save();

            NotificationService::notifyAdmin(
                'مريض جديد من الموقع: ' . $name,
                route('Patients.show', $patient->id)
            );

            return $patient->fresh(['translations']);
        });
    }
}
