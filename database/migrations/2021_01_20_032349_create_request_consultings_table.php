<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

/**
 * Class CreateRequestConsultingsTable.
 */
class CreateRequestConsultingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('request_consultings', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('customer_id');
            $table->integer('owner_id');
            $table->integer('service_id');
            $table->text('message');
            $table->integer('progress')->default(0);
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
        Schema::drop('request_consultings');
    }
}
