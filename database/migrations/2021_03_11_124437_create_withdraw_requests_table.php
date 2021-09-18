<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWithdrawRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('withdraw_requests', function (Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->integer('user_id');
            $table->float('amount');
            $table->integer('trans_wallet_id')->nullable();
            $table->integer('trans_wallet_expire_id')->nullable();
            $table->string('state')->default('PENDING')->comment('PENDING | ACCEPTED | REJECTED');
            $table->datetime('date_accepted')->nullable();
            $table->datetime('date_rejected')->nullable();
            $table->text('reason_rejected')->nullable();
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
        Schema::dropIfExists('withdraw_requests');
    }
}
