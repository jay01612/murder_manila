<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ClientInfo extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('client_info', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->bigInteger('game_id')->unsigned();
            $table->foreign('game_id')->references('id')->on('booking_table');

            $table->string('fname');
            $table->string('lname');
            $table->string('mobile_number');

            $table->string('verification_number');

            $table->string('email');

            $table->tinyInteger('is_emailed');
            $table->tinyInteger('is_verified');

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
        Schema::dropIfExists('client_info');
    }
}
