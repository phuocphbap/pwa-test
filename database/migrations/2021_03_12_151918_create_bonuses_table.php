<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

/**
 * Class CreateBonusesTable.
 */
class CreateBonusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bonuses', function (Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->integer('user_id');
            $table->float('amount');
            $table->integer('trans_wallet_id')->nullable();
            $table->integer('trans_wallet_expire_id')->nullable();
            $table->string('type')->default('ADMIN')->comment('ADMIN | REFFERAL | INPUT_REFFERAL');
            $table->integer('user_input_refferal')->nullable();
            $table->text('reason_bonus')->nullable();
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
        Schema::drop('bonuses');
    }
}
