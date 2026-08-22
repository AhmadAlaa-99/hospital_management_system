<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('pharmacy_invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number')->unique();
            $table->foreignId('patient_id')->constrained('patients')->cascadeOnDelete();
            $table->foreignId('diagnostic_id')->nullable()->constrained('diagnostics')->nullOnDelete();
            $table->foreignId('doctor_id')->nullable()->constrained('doctors')->nullOnDelete();
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('total_amount', 12, 2)->default(0);
            $table->unsignedBigInteger('dispensed_by')->nullable();
            $table->string('dispensed_by_type')->default('admin');
            $table->text('notes')->nullable();
            $table->timestamp('issued_at')->useCurrent();
            $table->timestamps();
        });

        Schema::table('pharmacy_dispensings', function (Blueprint $table) {
            $table->foreignId('pharmacy_invoice_id')->nullable()->after('id')->constrained('pharmacy_invoices')->nullOnDelete();
        });

        Schema::table('prescriptions', function (Blueprint $table) {
            $table->boolean('is_dispensed')->default(false)->after('instructions');
            $table->timestamp('dispensed_at')->nullable()->after('is_dispensed');
            $table->foreignId('medicine_id')->nullable()->after('dispensed_at')->constrained('medicines')->nullOnDelete();
        });
    }

    public function down()
    {
        Schema::table('prescriptions', function (Blueprint $table) {
            $table->dropForeign(['medicine_id']);
            $table->dropColumn(['is_dispensed', 'dispensed_at', 'medicine_id']);
        });

        Schema::table('pharmacy_dispensings', function (Blueprint $table) {
            $table->dropForeign(['pharmacy_invoice_id']);
            $table->dropColumn('pharmacy_invoice_id');
        });

        Schema::dropIfExists('pharmacy_invoices');
    }
};
