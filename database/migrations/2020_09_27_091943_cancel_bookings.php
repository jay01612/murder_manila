<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CancelBookings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cancel_bookings', function (Blueprint $table) {

            $table->bigIncrements('id');

            $table->bigInteger('client_id')->unsigned();
            $table->foreign('client_id')->references('id')->on('client_info');
            
            $table->bigInteger('booking_id')->unsigned();
            $table->foreign('booking_id')->references('id')->on('booking_table');

            $table->date('reschedule_date');
            $table->time('reschedule_time');

            $table->bigInteger('updated_by')->nullable();
            
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
        Schema::dropIfExists('cancel_bookings');
    }
}
