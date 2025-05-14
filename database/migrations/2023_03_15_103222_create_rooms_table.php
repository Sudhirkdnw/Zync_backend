<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRoomsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            // $table->integer('my_user_id');
            $table->integer('admin_id');
            $table->string('photo')->nullable();
            $table->string('title');
            $table->string('desc');
            $table->string('interests');
            $table->integer('room_status');
            $table->integer('join_after_request');
            $table->integer('total_member')->default(0);
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
        Schema::dropIfExists('rooms');
    }
}
