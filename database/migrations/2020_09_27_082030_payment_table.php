<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class PaymentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payment_table', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->bigInteger('reference_id')->unsigned();
            $table->foreign('reference_id')->references('id')->on('booking_table');

            $table->bigInteger('client_id')->unsigned();
            $table->foreign('client_id')->references('id')->on('client_info');

            $table->tinyInteger('is_paid')->default(0);

            $table->time('paid_time')->nullable();
            $table->date('paid_date')->nullable();

            $table->string('amount');

            $table->tinyInteger('is_emailed')->default(0);
            
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
        Schema::dropIfExists('payment_table');
    }
}
