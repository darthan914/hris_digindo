<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBookContract extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('book_contract', function (Blueprint $table) {
            $table->increments('id')->comment('akan bertambah otomatis bila buat karyawan baru atau perubahan kontrak');
            $table->integer('id_employee');
            $table->date('date_change');
            $table->string('type_contract')->comment('contract, part-time, permanent');
            $table->date('date_contract')->comment('tanggal buat kontrak kerja');
            $table->date('end_contract')->comment('tanggal kadaluarsa kontrak');
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
        Schema::dropIfExists('book_contract');
    }
}
