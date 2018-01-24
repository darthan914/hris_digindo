<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmployee extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employee', function (Blueprint $table) {
            $table->increments('id');

            // Data Pribadi
            $table->string('name');
            $table->date('birthday');
            $table->string('gender')->comment('male, female');
            $table->string('religion')->comment('islam, kristen, buddha, hindu, khatolik, dll');
            $table->string('no_ktp');
            $table->string('status')->comment('single, menikah');
            $table->text('ktp_address');
            $table->text('current_address')->nullable();
            $table->text('npwp')->nullable();
            $table->text('npwp_address')->nullable();
            $table->string('npwp_status')->nullable();
            $table->text('phone');

            // Data Karyawan
            $table->date('date_join');
            $table->string('nik')->unique();
            $table->string('job_title');
            $table->string('division')->nullable();
            $table->string('sub_division')->nullable();
            $table->string('level');
            $table->integer('id_leader')->nullable()->comment('atasan id employee');
            $table->integer('id_absence_machine')->nullable();

            // Data Kontrak
            $table->string('type_contract')->comment('contract, part-time, permanent');
            $table->date('start_date_contract');
            $table->date('end_date_contract');
            $table->integer('id_shift');
            $table->boolean('need_book_overtime')->default(0);
            $table->integer('min_overtime')->default(0)->comment('hitungan permenit');
            $table->text('guarantee')->nullable();
            $table->boolean('status_guarantee')->comment('0 = tidak disimpan, 1 = disimpan');

            // Data Gaji
            $table->double('gaji_pokok', 12, 2)->default(0);
            $table->double('tunjangan', 12, 2)->default(0);
            $table->double('perawatan_motor', 12, 2)->default(0);
            $table->double('uang_makan', 12, 2)->default(0);
            $table->double('transport', 12, 2)->default(0);
            $table->double('bpjs_kesehatan', 12, 2)->default(0);
            $table->double('bpjs_ketenagakerjaan', 12, 2)->default(0);
            $table->double('uang_telat', 12, 2)->default(0);
            $table->integer('uang_telat_permenit')->default(15);
            $table->double('uang_lembur', 12, 2)->default(0);
            $table->integer('uang_lembur_permenit')->default(15);
            $table->double('pph', 12, 2)->default(0);
            $table->date('update_payroll');

            // Data Test Karyawan
            $table->text('test_disc')->nullable();
            $table->text('test_gratyo')->nullable();
            $table->text('test_math')->nullable();

            // Data Darurat
            $table->string('emergency_name')->nullable();
            $table->text('emergency_phone')->nullable();
            $table->string('emergency_relation')->nullable();

            // Data Resign
            $table->boolean('status_resign')->default(0)->comment('0 = belum resign, 1 = sudah resign');
            $table->date('date_resign')->nullable();

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
        Schema::dropIfExists('employee');
    }
}
