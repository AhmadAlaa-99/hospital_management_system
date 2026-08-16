<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddUserTypeToNotificationsAndRefusedAppointments extends Migration
{
    public function up()
    {
        Schema::table('notifications', function (Blueprint $table) {
            if (!Schema::hasColumn('notifications', 'user_type')) {
                $table->string('user_type', 40)->nullable()->after('user_id')->index();
            }
        });

        // إضافة حالة مرفوض للمواعيد
        DB::statement("ALTER TABLE appointments MODIFY COLUMN type ENUM('غير مؤكد','مؤكد','منتهي','مرفوض') DEFAULT 'غير مؤكد'");
    }

    public function down()
    {
        Schema::table('notifications', function (Blueprint $table) {
            if (Schema::hasColumn('notifications', 'user_type')) {
                $table->dropColumn('user_type');
            }
        });

        DB::statement("ALTER TABLE appointments MODIFY COLUMN type ENUM('غير مؤكد','مؤكد','منتهي') DEFAULT 'غير مؤكد'");
    }
}
