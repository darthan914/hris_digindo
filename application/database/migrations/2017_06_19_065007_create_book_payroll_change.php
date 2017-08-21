<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBookPayrollChange extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('book_payroll_change', function (Blueprint $table) {
            $table->increments('id')->comment('akan bertambah otomatis bila buat karyawan baru atau perubahan gaji');
            $table->integer('id_employee');
            $table->date('date_change');
            $table->double('gaji_pokok', 12, 2);
            $table->double('tunjangan', 12, 2)->default(0);
            $table->double('perawatan_motor', 12, 2)->default(0);
            $table->double('uang_makan', 12, 2)->default(0);
            $table->double('transport', 12, 2)->default(0);
            $table->double('bpjs_kesehatan', 12, 2)->default(0);
            $table->double('bpjs_ketenagakerjaan', 12, 2)->default(0);
            $table->double('pph', 12, 2)->default(0);
            $table->double('uang_telat', 12, 2)->default(0)->comment('jumlah uang telat');
            $table->double('uang_lembur', 12, 2)->default(0);
            $table->date('update_payroll');
            $table->text('note');
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
        Schema::dropIfExists('book_payroll_change');
    }
}
