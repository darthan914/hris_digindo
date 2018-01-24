<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateShiftDetail extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shift_detail', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_shift');
            $table->integer('day')->comment('0= minggu, … , 6=sabtu');
            $table->time('shift_in');
            $table->time('shift_out');
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
        Schema::dropIfExists('shift_detail');
    }
}
