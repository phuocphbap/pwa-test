<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class UpdateBillsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bills', function ($table) {
            $table->integer('customer_id')->after('service_id');
            $table->integer('owner_id')->after('customer_id');
            $table->integer('customer_trans_id')->nullable()->after('owner_id');
            $table->integer('owner_trans_id')->nullable()->after('customer_trans_id');

            $table->dropColumn('user_id');
            $table->dropColumn('transaction_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bills', function ($table) {
            $table->dropColumn('customer_id');
            $table->dropColumn('owner_id');
            $table->dropColumn('customer_trans_id');
            $table->dropColumn('owner_trans_id');
            $table->integer('user_id');
            $table->integer('transaction_id')->nullable();
        });
    }
}
