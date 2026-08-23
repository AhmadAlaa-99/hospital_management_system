<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('referrals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('patients')->cascadeOnDelete();
            $table->foreignId('from_doctor_id')->constrained('doctors')->cascadeOnDelete();
            $table->foreignId('to_doctor_id')->constrained('doctors')->cascadeOnDelete();
            $table->foreignId('from_section_id')->nullable()->constrained('sections')->nullOnDelete();
            $table->foreignId('to_section_id')->nullable()->constrained('sections')->nullOnDelete();
            $table->foreignId('diagnostic_id')->nullable()->constrained('diagnostics')->nullOnDelete();
            $table->text('reason');
            $table->text('notes')->nullable();
            $table->enum('status', ['pending', 'accepted', 'completed', 'rejected'])->default('pending');
            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });

        Schema::create('follow_up_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('patients')->cascadeOnDelete();
            $table->foreignId('doctor_id')->constrained('doctors')->cascadeOnDelete();
            $table->foreignId('section_id')->nullable()->constrained('sections')->nullOnDelete();
            $table->foreignId('diagnostic_id')->nullable()->constrained('diagnostics')->nullOnDelete();
            $table->date('follow_up_date');
            $table->text('notes')->nullable();
            $table->enum('status', ['scheduled', 'completed', 'missed', 'cancelled'])->default('scheduled');
            $table->foreignId('appointment_id')->nullable()->constrained('appointments')->nullOnDelete();
            $table->boolean('reminder_sent')->default(false);
            $table->timestamps();
        });

        Schema::create('medical_certificates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('patients')->cascadeOnDelete();
            $table->foreignId('doctor_id')->constrained('doctors')->cascadeOnDelete();
            $table->foreignId('diagnostic_id')->nullable()->constrained('diagnostics')->nullOnDelete();
            $table->enum('type', ['sick_leave', 'fitness', 'medical_report']);
            $table->string('title');
            $table->text('content');
            $table->string('reference_number')->unique();
            $table->date('valid_from')->nullable();
            $table->date('valid_until')->nullable();
            $table->unsignedSmallInteger('days_off')->nullable();
            $table->timestamp('issued_at')->useCurrent();
            $table->timestamps();
        });

        Schema::create('external_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('patients')->cascadeOnDelete();
            $table->string('title');
            $table->enum('type', ['lab', 'ray', 'report', 'prescription', 'other'])->default('other');
            $table->string('file_path');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('medicines', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('generic_name')->nullable();
            $table->unsignedInteger('quantity')->default(0);
            $table->decimal('unit_price', 10, 2)->default(0);
            $table->date('expiry_date')->nullable();
            $table->unsignedInteger('min_stock_level')->default(10);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('pharmacy_dispensings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('patients')->cascadeOnDelete();
            $table->foreignId('diagnostic_id')->nullable()->constrained('diagnostics')->nullOnDelete();
            $table->foreignId('prescription_id')->nullable()->constrained('prescriptions')->nullOnDelete();
            $table->foreignId('medicine_id')->constrained('medicines')->cascadeOnDelete();
            $table->unsignedInteger('quantity_dispensed');
            $table->decimal('unit_price', 10, 2);
            $table->decimal('total_price', 10, 2);
            $table->unsignedBigInteger('dispensed_by')->nullable();
            $table->string('dispensed_by_type')->default('admin');
            $table->timestamp('dispensed_at')->useCurrent();
            $table->timestamps();
        });

        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('user_type')->nullable();
            $table->string('action');
            $table->string('model_type')->nullable();
            $table->unsignedBigInteger('model_id')->nullable();
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();
            $table->index(['model_type', 'model_id']);
        });

        Schema::create('patient_package_usages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('patients')->cascadeOnDelete();
            $table->foreignId('group_id')->constrained('groups')->cascadeOnDelete();
            $table->foreignId('service_id')->constrained('Services')->cascadeOnDelete();
            $table->foreignId('invoice_id')->nullable()->constrained('invoices')->nullOnDelete();
            $table->unsignedInteger('quantity_allowed')->default(1);
            $table->unsignedInteger('quantity_used')->default(0);
            $table->timestamp('purchased_at')->useCurrent();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });

        Schema::create('ambulance_request_timelines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ambulance_request_id')->constrained('ambulance_requests')->cascadeOnDelete();
            $table->string('status');
            $table->text('notes')->nullable();
            $table->timestamp('recorded_at')->useCurrent();
            $table->timestamps();
        });

        Schema::table('ambulance_requests', function (Blueprint $table) {
            $table->enum('triage_level', ['critical', 'urgent', 'normal'])->default('normal')->after('notes');
            $table->foreignId('section_id')->nullable()->after('ambulance_id')->constrained('sections')->nullOnDelete();
            $table->foreignId('doctor_id')->nullable()->after('section_id')->constrained('doctors')->nullOnDelete();
            $table->foreignId('appointment_id')->nullable()->after('doctor_id')->constrained('appointments')->nullOnDelete();
            $table->boolean('transferred_to_clinic')->default(false)->after('appointment_id');
            $table->text('transfer_notes')->nullable()->after('transferred_to_clinic');
            $table->foreignId('patient_id')->nullable()->after('transfer_notes')->constrained('patients')->nullOnDelete();
        });

        DB::statement("ALTER TABLE ambulance_requests MODIFY COLUMN status ENUM('pending','dispatched','en_route','arrived','transported','completed','cancelled') NOT NULL DEFAULT 'pending'");

        Schema::table('ambulances', function (Blueprint $table) {
            $table->string('paramedic_name')->nullable()->after('driver_phone');
            $table->string('coverage_area')->nullable()->after('paramedic_name');
            $table->date('last_maintenance_date')->nullable()->after('coverage_area');
        });

        Schema::table('appointments', function (Blueprint $table) {
            $table->enum('consultation_type', ['in_person', 'telemedicine'])->default('in_person')->after('reminder_sent');
            $table->string('meeting_url')->nullable()->after('consultation_type');
            $table->boolean('is_emergency')->default(false)->after('meeting_url');
        });

        Schema::table('groups', function (Blueprint $table) {
            $table->boolean('is_health_package')->default(false)->after('Total_with_tax');
            $table->string('package_type')->nullable()->after('is_health_package');
            $table->unsignedSmallInteger('validity_days')->default(90)->after('package_type');
        });
    }

    public function down()
    {
        Schema::table('groups', function (Blueprint $table) {
            $table->dropColumn(['is_health_package', 'package_type', 'validity_days']);
        });

        Schema::table('appointments', function (Blueprint $table) {
            $table->dropColumn(['consultation_type', 'meeting_url', 'is_emergency']);
        });

        Schema::table('ambulances', function (Blueprint $table) {
            $table->dropColumn(['paramedic_name', 'coverage_area', 'last_maintenance_date']);
        });

        DB::statement("ALTER TABLE ambulance_requests MODIFY COLUMN status ENUM('pending','dispatched','completed','cancelled') NOT NULL DEFAULT 'pending'");

        Schema::table('ambulance_requests', function (Blueprint $table) {
            $table->dropForeign(['section_id']);
            $table->dropForeign(['doctor_id']);
            $table->dropForeign(['appointment_id']);
            $table->dropForeign(['patient_id']);
            $table->dropColumn([
                'triage_level', 'section_id', 'doctor_id', 'appointment_id',
                'transferred_to_clinic', 'transfer_notes', 'patient_id',
            ]);
        });

        Schema::dropIfExists('ambulance_request_timelines');
        Schema::dropIfExists('patient_package_usages');
        Schema::dropIfExists('activity_logs');
        Schema::dropIfExists('pharmacy_dispensings');
        Schema::dropIfExists('medicines');
        Schema::dropIfExists('external_records');
        Schema::dropIfExists('medical_certificates');
        Schema::dropIfExists('follow_up_plans');
        Schema::dropIfExists('referrals');
    }
};
