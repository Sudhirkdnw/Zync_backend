<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

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
            $table->id();
            $table->string('identity');
            $table->string('username');
            $table->string('full_name');
            $table->string('bio')->nullable();
            $table->string('interests')->nullable();
            $table->string('profile')->nullable();
            $table->integer('following')->nullable();
            $table->integer('followers')->nullable();
            $table->integer('login_type');
            $table->integer('device_type');
            $table->string('onesignal_player_id');
            $table->timestamps();
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
