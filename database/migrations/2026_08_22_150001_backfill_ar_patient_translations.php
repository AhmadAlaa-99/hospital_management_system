<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('patient_translations')) {
            return;
        }

        $englishRows = DB::table('patient_translations')
            ->where('locale', 'en')
            ->get();

        foreach ($englishRows as $row) {
            $hasArabic = DB::table('patient_translations')
                ->where('patient_id', $row->patient_id)
                ->where('locale', 'ar')
                ->exists();

            if ($hasArabic) {
                continue;
            }

            DB::table('patient_translations')->insert([
                'locale' => 'ar',
                'name' => $row->name,
                'Address' => $row->Address,
                'patient_id' => $row->patient_id,
            ]);
        }
    }

    public function down(): void
    {
        // لا حذف — بيانات تصحيحية
    }
};
