<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAbsenceEmployeeDetail extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('absence_employee_detail', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_absence_employee');
            $table->date('date');
            $table->time('shift_in')->nullable();
            $table->time('shift_out')->nullable();
            $table->time('check_in')->nullable();
            $table->time('check_out')->nullable();
            $table->string('status')->comment('masuk, sakit, izin, cuti, alpa, pending');
            $table->double('fine_additional', 12, 2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('absence_employee_detail');
    }
}
