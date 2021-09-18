<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

/**
 * Class CreateServiceReviewsTable.
 */
class CreateServiceReviewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('service_reviews', function (Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->integer('consulting_id');
            $table->integer('service_id');
            $table->integer('store_id');
            $table->integer('reviewer_id');
            $table->integer('is_owner')->defualt(0)->comment('0 not onwer | 1 is owner');
            $table->integer('value')->comment('0 unsatisfied | 1 medium | 2 satisfied');
            $table->text('message');
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
        Schema::drop('service_reviews');
    }
}
