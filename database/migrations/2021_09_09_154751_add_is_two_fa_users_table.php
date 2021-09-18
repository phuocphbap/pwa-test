<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsTwoFaUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->integer('is_two_fa')->default(0);
            $table->string('phone_otp_token')->nullable()->after('is_two_fa');
            $table->string('phone_verify')->nullable()->after('phone');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('is_two_fa');
            $table->dropColumn('phone_otp_token');
            $table->dropColumn('phone_verify');
        });
    }
}
