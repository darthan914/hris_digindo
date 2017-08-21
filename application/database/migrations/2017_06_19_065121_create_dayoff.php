<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDayoff extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dayoff', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_employee');
            $table->date('date')->comment('tanggal permintaan');
            $table->float('total_dayoff', 2, 1)->default(0)->comment('jumlah cuti yang dimasukan, khusus type = cuti');
            $table->date('start_dayoff')->comment('tanggal mulai cuti');
            $table->date('end_dayoff')->comment('tanggal akhir cuti');
            $table->boolean('half_day')->default(0)->comment('bila masuk siangan atau pulang cepat');
            $table->string('type')->comment('izin, cuti, sakit');
            $table->text('note')->comment('keterangan');
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
        Schema::dropIfExists('dayoff');
    }
}
