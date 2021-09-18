<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class CreateStoreIntroductionsTable.
 */
class CreateStoreIntroductionsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('store_introductions', function(Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->integer('store_id');
            $table->string('title')->nullable();
            $table->text('contents')->nullable();
            $table->string('image_path')->nullable();
            $table->integer('order')->default(1);
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
		Schema::drop('store_introductions');
	}
}
