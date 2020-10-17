<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class BookingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('booking_table', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('reference_number');

            $table->date('book_date');
            $table->time('book_time');

            $table->bigInteger('theme_id')->unsigned();
            $table->foreign('theme_id')->references('id')->on('themes');

            $table->bigInteger('discount_id')->unsigned()->default(1);
            $table->foreign('discount_id')->references('id')->on('discounts');

            $table->string('maxpax');
            $table->string('venue');

            $table->tinyInteger('is_booked')->default(0); 
            $table->tinyInteger('is_cancelled')->default(0);

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
        Schema::dropIfExists('booking_table');
    }
}
