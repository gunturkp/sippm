<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblJadwalPemeliharaanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_jadwal_pemeliharaan', function (Blueprint $table) {
            $table->increments('id_jadwal_pemeliharaan');
            $table->unsignedInteger('id_teknisi');
            $table->unsignedInteger('id_inventory');
            $table->tinyInteger('hari');
            $table->time('waktu');
            $table->tinyInteger('is_year')->default(0);
            $table->date('last_work')->nullable();
            $table->timestamps();
            
            $table->foreign('id_teknisi')->references('id_teknisi')->on('teknisi')->onUpdate('cascade')->onDelete('cascade');
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
        Schema::dropIfExists('tbl_jadwal_pemeliharaan');
    }
}
