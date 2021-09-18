<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class CreateAdvertisingMediaTable.
 */
class CreateAdvertisingMediaTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('advertising_media', function(Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->integer('block_id');
            $table->string('image_path')->nullable();
            $table->string('link_path')->nullable();
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
		Schema::drop('advertising_media');
	}
}
