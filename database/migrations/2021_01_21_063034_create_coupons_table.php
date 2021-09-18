<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

/**
 * Class CreateCouponsTable.
 */
class CreateCouponsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('coupons', function (Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->string('couponable_type')->nullable();
            $table->integer('couponable_id')->comment('services_id');
            $table->string('coupon_code');
            $table->float('coupon_discount')->default(0);
            $table->integer('quantity')->default(0);
            $table->datetime('start_date')->nullable();
            $table->datetime('expire_date')->nullable();
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
        Schema::drop('coupons');
    }
}
