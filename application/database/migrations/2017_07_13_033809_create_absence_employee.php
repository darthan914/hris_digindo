<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAbsenceEmployee extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('absence_employee', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_absence');
            $table->integer('id_employee')->nullable();
            $table->integer('id_absence_machine')->nullable();
            $table->integer('day_per_month')->comment('jumlah hari masuk per bulan');
            $table->double('gaji_pokok', 15, 2)->default(0);
            $table->double('tunjangan', 15, 2)->default(0);
            $table->double('perawatan_motor', 15, 2)->default(0);
            $table->double('uang_makan', 15, 2)->default(0);
            $table->double('transport', 15, 2)->default(0);
            $table->double('bpjs_kesehatan', 15, 2)->default(0);
            $table->double('bpjs_ketenagakerjaan', 15, 2)->default(0);
            $table->double('uang_telat', 15, 2)->default(0);
            $table->integer('uang_telat_permenit')->default(15);
            $table->double('uang_lembur', 15, 2)->default(0);
            $table->integer('uang_lembur_permenit')->default(15);
            $table->double('pph', 15, 2)->default(0);
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
        Schema::dropIfExists('absence_employee');
    }
}
