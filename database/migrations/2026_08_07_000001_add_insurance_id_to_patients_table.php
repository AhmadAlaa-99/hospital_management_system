<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddInsuranceIdToPatientsTable extends Migration
{
    public function up()
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->foreignId('insurance_id')
                ->nullable()
                ->after('Blood_Group')
                ->constrained('insurances')
                ->nullOnDelete();
        });
    }

    public function down()
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->dropForeign(['insurance_id']);
            $table->dropColumn('insurance_id');
        });
    }
}
