<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            if (!Schema::hasColumn('appointments', 'patient_id')) {
                $table->foreignId('patient_id')->nullable()->after('section_id')
                    ->constrained('patients')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            if (Schema::hasColumn('appointments', 'patient_id')) {
                $table->dropForeign(['patient_id']);
                $table->dropColumn('patient_id');
            }
        });
    }
};
