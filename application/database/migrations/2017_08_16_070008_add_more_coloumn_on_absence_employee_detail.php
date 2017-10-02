<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMoreColoumnOnAbsenceEmployeeDetail extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('absence_employee_detail', function (Blueprint $table) {
            $table->datetime('schedule_in')->nullable();
            $table->datetime('schedule_out')->nullable();
            $table->string('status')->comment('masuk,izin,cuti,sakit,alpha,libur');
            $table->text('status_note')->nullable();
            $table->float('gaji', 12, 2)->comment('jika ada masuk hari libur tambah 0.5');
            $table->double('gaji_pokok');
            $table->datetime('time_overtime')->nullable();
            $table->float('point_overtime')->comment('antara time_out dan time_overtime ambil tercepat');
            $table->double('payment_overtime', 12, 2);
            $table->integer('total_late');
            $table->double('fine_late', 12, 2);
            $table->double('fine_additional', 12, 2)->default(0)->comment('denda tambahan');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('absence_employee_detail', function (Blueprint $table) {
            $table->dropColumn('schedule_in');
            $table->dropColumn('schedule_out');
            $table->dropColumn('status');
            $table->dropColumn('status_note');
            $table->dropColumn('gaji');
            $table->dropColumn('gaji_pokok');
            $table->dropColumn('time_overtime');
            $table->dropColumn('point_overtime');
            $table->dropColumn('payment_overtime');
            $table->dropColumn('total_late');
            $table->dropColumn('fine_late');
            $table->dropColumn('fine_additional');
        });
    }
}
