<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

/**
 * Class CreateAdvertisingsTable.
 */
class CreateAdvertisingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('advertisings', function (Blueprint $table) {
            $table->increments('id');
            $table->string('ad_title');
            $table->text('ad_description');
            $table->string('ad_position');
            $table->string('ad_page');
            $table->string('ad_image');
            $table->text('ad_link');
            $table->integer('ad_order');

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
        Schema::drop('advertisings');
    }
}
