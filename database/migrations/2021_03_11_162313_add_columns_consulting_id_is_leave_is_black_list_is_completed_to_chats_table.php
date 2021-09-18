<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsConsultingIdIsLeaveIsBlackListIsCompletedToChatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('chats', function (Blueprint $table) {
            $table->integer('consulting_id')->after('key_firebase');
            $table->boolean('is_completed')->default(0);
            $table->boolean('is_leave')->default(0);
            $table->boolean('is_black_list')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('chats', function (Blueprint $table) {
            $table->dropColumn('consulting_id');
            $table->dropColumn('is_completed');
            $table->dropColumn('is_leave');
            $table->dropColumn('is_black_list');
        });
    }
}
