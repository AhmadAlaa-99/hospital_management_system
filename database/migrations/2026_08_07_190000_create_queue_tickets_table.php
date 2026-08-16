<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQueueTicketsTable extends Migration
{
    public function up()
    {
        Schema::create('queue_tickets', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_number');
            $table->unsignedInteger('daily_sequence');
            $table->date('queue_date');
            $table->foreignId('section_id')->constrained('sections')->cascadeOnDelete();
            $table->foreignId('doctor_id')->nullable()->constrained('doctors')->nullOnDelete();
            $table->foreignId('appointment_id')->nullable()->constrained('appointments')->nullOnDelete();
            $table->foreignId('patient_id')->nullable()->constrained('patients')->nullOnDelete();
            $table->string('patient_name');
            $table->string('phone')->nullable();
            $table->enum('status', ['waiting', 'called', 'serving', 'completed', 'no_show', 'cancelled'])->default('waiting');
            $table->enum('priority', ['normal', 'urgent', 'elderly'])->default('normal');
            $table->unsignedSmallInteger('estimated_wait_minutes')->default(0);
            $table->timestamp('issued_at')->useCurrent();
            $table->timestamp('called_at')->nullable();
            $table->timestamp('serving_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->unique(['queue_date', 'section_id', 'daily_sequence']);
            $table->index(['queue_date', 'section_id', 'doctor_id', 'status']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('queue_tickets');
    }
}
