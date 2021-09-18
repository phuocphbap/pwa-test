<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('identity_status')->default('PENDING')->after('is_email_verified');
            $table->string('input_refferal_code')->nullable()->after('referral_code');
            $table->string('phone_verify_token')->nullable()->after('phone');
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
            $table->dropColumn('identity_status');
            $table->dropColumn('input_refferal_code');
            $table->dropColumn('phone_verify_token');
        });
    }
}
