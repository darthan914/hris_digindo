<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Add4ColumnOnEmployee extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('employee', function (Blueprint $table) {
            $table->string('level')->comment('leader, staff')->after('id_job_title');
            $table->integer('leader')->default(0)->comment('bila level staff harus pilih employee id, bila leader, 0');
            $table->text('npwp')->nullable()->comment('nomor pokok wajib pajak')->after('no_ktp');
            $table->double('uang_telat', 12, 2)->default(0)->comment('jumlah uang telat')->after('pph');
            $table->integer('id_machine')->default(0)->comment('nomor id mesin absen');
            $table->double('uang_lembur', 12, 2)->default(0);
            $table->date('update_payroll');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('employee', function (Blueprint $table) {
            $table->dropColumn('level');
            $table->dropColumn('leader');
            $table->dropColumn('npwp');
            $table->dropColumn('uang_telat');
            $table->dropColumn('uang_lembur');
        });
    }
}
