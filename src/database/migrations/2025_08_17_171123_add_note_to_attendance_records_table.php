<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNoteToAttendanceRecordsTable extends Migration
{
    public function up()
    {
        Schema::table('attendance_records', function (Blueprint $table) {
            $table->text('note')->nullable()->comment('修正申請の備考')->after('status');
        });
    }

    public function down()
    {
        Schema::table('attendance_records', function (Blueprint $table) {
            $table->dropColumn('note');
        });
    }
}
