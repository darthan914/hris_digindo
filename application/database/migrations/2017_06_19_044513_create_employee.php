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
            $table->string('name');
            $table->date('birthday');
            $table->string('gender')->comment('male, female');
            $table->string('region')->comment('islam, kristen, buddha, hindu, khatolik, dll');
            $table->string('status')->comment('single, menikah');
            $table->string('ktp_address');
            $table->string('current_address')->nullable();
            $table->string('no_ktp');
            $table->string('nik')->unique();
            $table->integer('id_job_title');
            $table->string('division')->nullable();
            $table->string('sub_division')->nullable();
            $table->date('date_join');
            $table->text('phone');
            $table->text('guarantee')->nullable();
            $table->text('ref')->nullable();
            $table->text('test_disc')->nullable();
            $table->text('test_gratyo')->nullable();
            $table->text('test_math')->nullable();
            $table->string('emergency_name')->nullable();
            $table->text('emergency_phone')->nullable();
            $table->string('emergency_relation')->nullable();
            $table->string('type_contract')->comment('contract, part-time, permanent');
            $table->date('date_contract')->comment('tanggal buat kontrak kerja');
            $table->date('end_contract')->comment('tanggal terakhir kontrak jika type_contract = permanent diabaikan');
            $table->double('gaji_pokok', 12, 2);
            $table->double('tunjangan', 12, 2)->default(0);
            $table->double('perawatan_motor', 12, 2)->default(0);
            $table->double('uang_makan', 12, 2)->default(0);
            $table->double('transport', 12, 2)->default(0);
            $table->double('bpjs_kesehatan', 12, 2)->default(0);
            $table->double('bpjs_ketenagakerjaan', 12, 2)->default(0);
            $table->double('pph', 12, 2)->default(0);
            $table->date('date_resign')->nullable()->comment('jika kosong karyawan status aktif jika tidak status resignbila sudah melewati hari');

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
