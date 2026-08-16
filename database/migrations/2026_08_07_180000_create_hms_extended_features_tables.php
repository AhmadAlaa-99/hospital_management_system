<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHmsExtendedFeaturesTables extends Migration
{
    public function up()
    {
        Schema::create('doctor_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('doctor_id')->constrained('doctors')->cascadeOnDelete();
            $table->unsignedTinyInteger('day_of_week'); // 0=Sunday .. 6=Saturday
            $table->time('start_time');
            $table->time('end_time');
            $table->unsignedSmallInteger('slot_duration')->default(30);
            $table->timestamps();
            $table->unique(['doctor_id', 'day_of_week']);
        });

        Schema::create('prescriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('diagnostic_id')->constrained('diagnostics')->cascadeOnDelete();
            $table->string('medicine_name');
            $table->string('dosage')->nullable();
            $table->string('frequency')->nullable();
            $table->unsignedSmallInteger('duration_days')->nullable();
            $table->text('instructions')->nullable();
            $table->timestamps();
        });

        Schema::create('insurance_claims', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('patients')->cascadeOnDelete();
            $table->foreignId('insurance_id')->constrained('insurances')->cascadeOnDelete();
            $table->foreignId('invoice_id')->constrained('invoices')->cascadeOnDelete();
            $table->decimal('total_amount', 12, 2);
            $table->decimal('discount_amount', 12, 2)->default(0);
            $table->decimal('company_amount', 12, 2)->default(0);
            $table->decimal('patient_amount', 12, 2)->default(0);
            $table->enum('status', ['pending', 'approved', 'rejected', 'paid'])->default('pending');
            $table->date('claim_date');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('doctor_ratings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('doctor_id')->constrained('doctors')->cascadeOnDelete();
            $table->foreignId('patient_id')->constrained('patients')->cascadeOnDelete();
            $table->foreignId('appointment_id')->constrained('appointments')->cascadeOnDelete();
            $table->unsignedTinyInteger('rating');
            $table->text('comment')->nullable();
            $table->timestamps();
            $table->unique('appointment_id');
        });

        Schema::create('ambulance_requests', function (Blueprint $table) {
            $table->id();
            $table->string('patient_name');
            $table->string('phone');
            $table->text('address');
            $table->text('notes')->nullable();
            $table->foreignId('ambulance_id')->nullable()->constrained('ambulances')->nullOnDelete();
            $table->enum('status', ['pending', 'dispatched', 'completed', 'cancelled'])->default('pending');
            $table->timestamp('requested_at')->useCurrent();
            $table->timestamps();
        });

        Schema::table('appointments', function (Blueprint $table) {
            $table->date('preferred_date')->nullable()->after('notes');
            $table->time('preferred_time')->nullable()->after('preferred_date');
            $table->boolean('reminder_sent')->default(false)->after('preferred_time');
        });
    }

    public function down()
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropColumn(['preferred_date', 'preferred_time', 'reminder_sent']);
        });
        Schema::dropIfExists('ambulance_requests');
        Schema::dropIfExists('doctor_ratings');
        Schema::dropIfExists('insurance_claims');
        Schema::dropIfExists('prescriptions');
        Schema::dropIfExists('doctor_schedules');
    }
}
