<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUniqueAppointmentQueueTicket extends Migration
{
    public function up()
    {
        Schema::table('queue_tickets', function (Blueprint $table) {
            $table->unique(['queue_date', 'appointment_id'], 'queue_tickets_date_appointment_unique');
        });
    }

    public function down()
    {
        Schema::table('queue_tickets', function (Blueprint $table) {
            $table->dropUnique('queue_tickets_date_appointment_unique');
        });
    }
}
