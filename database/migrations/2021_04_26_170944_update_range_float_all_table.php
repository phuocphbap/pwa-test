<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateRangeFloatAllTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('wallets', function (Blueprint $table) {
            $table->float('amount', 12, 2)->default(0)->change();
        });
        Schema::table('services', function (Blueprint $table) {
            $table->float('price', 12, 2)->change();
        });
        Schema::table('wallet_transactions', function (Blueprint $table) {
            $table->float('amount', 12, 2)->nullable()->change();
        });
        Schema::table('wallet_expires', function (Blueprint $table) {
            $table->float('amount', 12, 2)->default(0)->change();
        });
        Schema::table('bills', function (Blueprint $table) {
            $table->float('point', 12, 2)->default(0)->change();
            $table->float('price', 12, 2)->default(0)->change();
            $table->float('amount', 12, 2)->default(0)->change();
        });
        Schema::table('withdraw_requests', function (Blueprint $table) {
            $table->float('amount', 12, 2)->change();
        });
        Schema::table('bonuses', function (Blueprint $table) {
            $table->float('amount', 12, 2)->change();
        });
        Schema::table('referral_bonuses', function (Blueprint $table) {
            $table->float('amount', 12, 2)->change();
        });
        Schema::table('request_consultings', function (Blueprint $table) {
            $table->float('price_requested', 12, 2)->default(0)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('wallets', function (Blueprint $table) {
            $table->float('amount')->default(0)->change();
        });
        Schema::table('services', function (Blueprint $table) {
            $table->float('price')->change();
        });
        Schema::table('wallet_transactions', function (Blueprint $table) {
            $table->float('amount')->nullable()->change();
        });
        Schema::table('wallet_expires', function (Blueprint $table) {
            $table->float('amount')->default(0)->change();
        });
        Schema::table('bills', function (Blueprint $table) {
            $table->float('point')->change();
            $table->float('price')->default(0)->change();
            $table->float('amount')->default(0)->change();
        });
        Schema::table('withdraw_requests', function (Blueprint $table) {
            $table->float('amount')->change();
        });
        Schema::table('bonuses', function (Blueprint $table) {
            $table->float('amount')->change();
        });
        Schema::table('referral_bonuses', function (Blueprint $table) {
            $table->float('amount')->change();
        });
        Schema::table('request_consultings', function (Blueprint $table) {
            $table->float('price_requested')->default(0)->change();
        });
    }
}
