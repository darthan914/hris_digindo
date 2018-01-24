<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateItemBorrowed extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('borrow', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_employee');
            $table->text('item');
            $table->date('date_borrow')->nullable();
            $table->date('date_return')->nullable();
            $table->text('note')->nullable();
            $table->boolean('status')->default(0)->comment('0 = belum dipinjam/dikembalikan, 1 = dipinjam');
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
        Schema::dropIfExists('borrow');
    }
}
