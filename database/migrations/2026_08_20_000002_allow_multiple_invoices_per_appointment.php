<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AllowMultipleInvoicesPerAppointment extends Migration
{
    public function up()
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropForeign(['appointment_id']);
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->dropUnique(['appointment_id']);
            $table->index('appointment_id');
            $table->foreign('appointment_id')
                ->references('id')
                ->on('appointments')
                ->nullOnDelete();
        });
    }

    public function down()
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropForeign(['appointment_id']);
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->dropIndex(['appointment_id']);
            $table->unique('appointment_id');
            $table->foreign('appointment_id')
                ->references('id')
                ->on('appointments')
                ->nullOnDelete();
        });
    }
}
