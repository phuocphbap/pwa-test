<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class CreateChatsTable.
 */
class CreateChatsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('chats', function(Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->string('key_firebase')->unique();
            $table->string('room_name')->nullable();
            $table->integer('owner_id');
            $table->integer('customer_id');
            $table->integer('service_id');
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
		Schema::drop('chats');
	}
}
