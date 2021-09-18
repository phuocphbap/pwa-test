<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class CreateAdvertisingBlocksTable.
 */
class CreateAdvertisingBlocksTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('advertising_blocks', function(Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->integer('category_id');
            $table->text('contents')->nullable();
            $table->string('name');
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
		Schema::drop('advertising_blocks');
	}
}
