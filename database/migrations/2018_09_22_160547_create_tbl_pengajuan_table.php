<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblPengajuanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_pengajuan', function (Blueprint $table) {
            $table->increments('id_pengajuan');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('id_inventory');
            $table->datetime('respon_time')->nullable();
            $table->datetime('done_time')->nullable();
            $table->tinyInteger('step'); // 0 Pengajuan baru; 0/1 Penugasan Kasubag; 1/2 Penerimaan Perbaikan Teknisi; 2/3 Penyelesaian Analisis Teknisi (+ Pengajuan Suku Cadang); 3/4 Penyelesaian Perbaikan Teknisi; 4/5/3 Konfirmasi User; 6 Ditolak
            $table->text('keluhan');                                                      
            $table->text('analisis')->nullable();
            $table->text('tindakan_perbaikan')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('id_inventory')->references('id_inventory')->on('master_inventory')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tbl_pengajuan');
    }
}
