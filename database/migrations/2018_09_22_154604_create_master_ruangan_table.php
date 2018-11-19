<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMasterRuanganTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('master_ruangan', function (Blueprint $table) {
            $table->increments('id_ruangan');
            $table->unsignedInteger('id_tipe_ruangan');
            $table->string('nama_ruangan',255);
            $table->integer('jarak');
            $table->timestamps();

            $table->foreign('id_tipe_ruangan')->references('id_tipe_ruangan')->on('master_tipe_ruangan')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('master_ruangan');
    }
}
