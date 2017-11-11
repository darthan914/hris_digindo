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
            $table->integer('id_machine');
            $table->integer('per_day')->default(0)->comment('jumlah hari masuk per bulan');
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
