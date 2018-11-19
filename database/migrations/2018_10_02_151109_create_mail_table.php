<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mail', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id_sender');
            $table->unsignedInteger('user_id_receiver');
            $table->text('message');
            $table->tinyInteger('is_read')->default(0);
            $table->timestamps();

            $table->foreign('user_id_sender')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('user_id_receiver')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mail');
    }
}
