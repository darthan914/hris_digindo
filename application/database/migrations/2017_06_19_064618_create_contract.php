<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateContract extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contract', function (Blueprint $table) {
            $table->increments('id')->comment('akan bertambah otomatis bila buat karyawan baru atau perubahan kontrak');
            $table->integer('id_employee')->comment('relasi dengan table employee column id');
            $table->string('type_contract')->comment('contract, part-time, permanent');
            $table->date('start_date_contract');
            $table->date('end_date_contract');
            $table->integer('id_shift');
            $table->boolean('need_book_overtime')->default(0);
            $table->integer('min_overtime')->default(0)->comment('hitungan permenit');
            $table->string('guarantee')->nullable();
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
        Schema::dropIfExists('contract');
    }
}
