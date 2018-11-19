<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblListSukuCadangPemeliharaanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_list_suku_cadang_pemeliharaan', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('id_pemeliharaan');
            $table->unsignedInteger('id_suku_cadang');
            $table->integer('jumlah');
            $table->tinyInteger('is_approve')->default(0);
            $table->timestamps();

            $table->foreign('id_suku_cadang')->references('id_suku_cadang')->on('master_suku_cadang')->onUpdate('cascade')->onDelete('cascade');
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
        Schema::dropIfExists('tbl_list_suku_cadang_pemeliharaan');
    }
}