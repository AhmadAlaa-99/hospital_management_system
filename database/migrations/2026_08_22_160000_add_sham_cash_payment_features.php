<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->boolean('sham_cash_enabled')->default(true)->after('copyright');
            $table->string('sham_cash_wallet')->nullable()->after('sham_cash_enabled');
            $table->string('sham_cash_qr_path')->nullable()->after('sham_cash_wallet');
            $table->text('sham_cash_instructions')->nullable()->after('sham_cash_qr_path');
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->enum('payment_status', ['unpaid', 'pending_review', 'paid', 'rejected'])
                ->default('unpaid')
                ->after('invoice_status');
        });

        Schema::create('sham_cash_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained('invoices')->cascadeOnDelete();
            $table->foreignId('patient_id')->constrained('patients')->cascadeOnDelete();
            $table->decimal('amount', 12, 2);
            $table->enum('status', ['pending_review', 'approved', 'rejected'])->default('pending_review');
            $table->string('receipt_path');
            $table->string('transaction_reference')->nullable();
            $table->text('patient_notes')->nullable();
            $table->text('admin_notes')->nullable();
            $table->unsignedBigInteger('reviewed_by')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('sham_cash_payments');

        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn('payment_status');
        });

        Schema::table('site_settings', function (Blueprint $table) {
            $table->dropColumn(['sham_cash_enabled', 'sham_cash_wallet', 'sham_cash_qr_path', 'sham_cash_instructions']);
        });
    }
};
