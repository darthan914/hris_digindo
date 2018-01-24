<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePayroll extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payroll', function (Blueprint $table) {
            $table->increments('id')->comment('akan bertambah otomatis bila buat karyawan baru atau perubahan gaji');
            $table->integer('id_employee')->comment('relasi dengan table employee column id');
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
            $table->date('update_payroll');
            $table->text('note');
            $table->date('date_change');
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
        Schema::dropIfExists('payroll');
    }
}
