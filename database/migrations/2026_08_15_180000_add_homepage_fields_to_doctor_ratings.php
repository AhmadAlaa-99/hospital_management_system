<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('doctor_ratings', function (Blueprint $table) {
            $table->boolean('share_on_homepage')->default(false)->after('comment');
            $table->enum('homepage_status', ['none', 'pending', 'approved', 'rejected'])
                ->default('none')
                ->after('share_on_homepage');
        });
    }

    public function down(): void
    {
        Schema::table('doctor_ratings', function (Blueprint $table) {
            $table->dropColumn(['share_on_homepage', 'homepage_status']);
        });
    }
};
