<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmployeeFamily extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employee_family', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_employee');
            $table->string('relation')->comment('ayah, ibu, saudara, suami, istri, anak');
            $table->string('name');
            $table->integer('age')->nullable();
            $table->string('school')->nullable()->comment('sd, smp, sma, smk, dll');
            $table->string('job')->nullable();
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
        Schema::dropIfExists('employee_family');
    }
}
