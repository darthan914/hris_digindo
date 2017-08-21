<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Add2ColumnOnJobTitle extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('job_title', function (Blueprint $table) {
            $table->boolean('book_overtime')->default(0)->comment('jika iya maka yang melewati jam pulang diperlukan data overtime bila tidak ada tidak ada uang lembur, jika tidak tidak perlu data overtime')->after('code');
            $table->integer('min_overtime')->default(0)->comment('minimal lembur dalam menit, bila kurang tidak dapat uang lembur');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('job_title', function (Blueprint $table) {
            $table->dropColumn('book_overtime');
            $table->dropColumn('min_overtime');
        });
    }
}
