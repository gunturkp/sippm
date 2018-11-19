<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->tinyInteger('role')->default(0);
            $table->timestamp('last_login')->useCurrent();
            $table->rememberToken();
            $table->timestamps();
            $table->string('id_tipe_ruangan');

            $table->foreign('id_tipe_ruangan')->references('id_tipe_ruangan')->on('master_tipe_ruangan')->onUpdate('cascade')->onDelete('setnull');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
