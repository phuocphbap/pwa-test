<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

/**
 * Class CreateUsersTable.
 */
class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->string('email')->unique()->nullable();
            $table->string('user_name');
            $table->string('password');
            $table->text('avatar')->nullable();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('gender')->nullable();
            $table->text('detail')->nullable();
            $table->string('address_id')->nullable();
            $table->string('phone')->nullable();
            $table->integer('is_phone_verified')->default(0);
            $table->integer('is_email_verified')->default(0);
            $table->date('birth_date')->nullable();
            $table->string('referral_code')->nullable();
            $table->integer('ranking')->default(0);
            $table->text('reason_leave')->nullable();
            $table->integer('state')->default(0);

            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
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
        Schema::drop('users');
    }
}
