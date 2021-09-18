<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStorePostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('store_posts', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('store_id');
            $table->text('link');
            $table->integer('order')->default(0);
            $table->string('type')->default('news')->comment('news|images');
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
        Schema::dropIfExists('store_posts');
    }
}
