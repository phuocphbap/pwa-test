<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsBlockedReasonBlockedSortTimeSortToServicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('services', function (Blueprint $table) {
            $table->boolean('is_blocked')->default(0);
            $table->integer('sort')->default(0);
            $table->string('reason_blocked')->nullable();
            $table->timestamp('time_sort', 6)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn('is_blocked');
            $table->dropColumn('sort');
            $table->dropColumn('reason_blocked');
            $table->dropColumn('time_sort');
        });
    }
}
