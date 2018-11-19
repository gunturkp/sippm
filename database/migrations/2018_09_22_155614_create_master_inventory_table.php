<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMasterInventoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('master_inventory', function (Blueprint $table) {
            $table->increments('id_inventory');
            $table->unsignedInteger('id_ruangan');
            $table->unsignedInteger('id_tipe_inventory');
            $table->string('kode_alat',255);
            $table->string('nama_alat',255);
            $table->string('merk_barang',255);
            $table->text('spesifikasi');
            $table->string('serial_number',100);
            $table->timestamps();

            $table->foreign('id_ruangan')->references('id_ruangan')->on('master_ruangan')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('id_tipe_inventory')->references('id_tipe_inventory')->on('master_tipe_inventory')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('master_inventory');
    }
}
