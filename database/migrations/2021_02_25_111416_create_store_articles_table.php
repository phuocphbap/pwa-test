<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class CreateStoreArticlesTable.
 */
class CreateStoreArticlesTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('store_articles', function(Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->integer('store_id');
            $table->string('title')->nullable();
            $table->text('contents')->nullable();
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
		Schema::drop('store_articles');
	}
}
