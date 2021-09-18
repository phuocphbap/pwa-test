<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBillsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bills', function (Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->string('title')->nullable();
            $table->integer('payment_id');
            $table->integer('service_id');
            $table->integer('user_id');
            $table->integer('transaction_id')->nullable();
            $table->integer('coupon_id')->nullable();
            $table->float('point');
            $table->float('price')->default(0);
            $table->float('amount')->default(0);
            $table->text('note')->nullable();
            $table->integer('state')->default(1);
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
        Schema::dropIfExists('bills');
    }
}
