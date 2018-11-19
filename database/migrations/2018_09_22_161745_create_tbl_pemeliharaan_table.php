<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblPemeliharaanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_pemeliharaan', function (Blueprint $table) {
            $table->increments('id_pemeliharaan');
            $table->unsignedInteger('id_jadwal_pemeliharaan');
            $table->datetime('waktu_realisasi');
            $table->datetime('respon_time')->nullable();
            $table->datetime('done_time')->nullable();
            $table->tinyInteger('step')->default(0);
            $table->text('analisis');
            $table->text('tindakan_perbaikan');
            $table->timestamps();
            
            $table->foreign('id_jadwal_pemeliharaan')->references('id_jadwal_pemeliharaan')->on('tbl_jadwal_pemeliharaan')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tbl_pemeliharaan');
    }
}
