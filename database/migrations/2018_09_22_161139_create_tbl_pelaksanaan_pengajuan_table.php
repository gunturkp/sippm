<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblPelaksanaanPengajuanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_pelaksanaan_pengajuan', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('id_teknisi');
            $table->unsignedInteger('id_pengajuan');
            $table->timestamps();

            $table->foreign('id_teknisi')->references('id_teknisi')->on('teknisi')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('id_pengajuan')->references('id_pengajuan')->on('tbl_pengajuan')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tbl_pelaksanaan_pengajuan');
    }
}
