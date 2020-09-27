<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Users extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('fname');
            $table->string('lname');
            $table->string("username");
            $table->string('password');
            $table->string('email');

            $table->bigInteger('position_id')->unsigned();;
            $table->foreign('position_id')->references('id')->on('access_levels');

            $table->tinyInteger('is_active')->default(1);

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
        //
    }
}
