<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblPelaksanaanPemeliharaanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_pelaksanaan_pemeliharaan', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('id_teknisi');
            $table->unsignedInteger('id_pemeliharaan');
            $table->timestamps();
            
            $table->foreign('id_teknisi')->references('id_teknisi')->on('teknisi')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('id_pemeliharaan')->references('id_pemeliharaan')->on('tbl_pemeliharaan')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tbl_pelaksanaan_pemeliharaan');
    }
}
