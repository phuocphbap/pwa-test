<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class CreateCompanyTermsTable.
 */
class CreateCompanyTermsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('company_terms', function(Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->string('file_name');
            $table->string('file_path');
            $table->string('file_size');
            $table->string('type')->comment('TERMS_OF_USE|SYMBOL|PRIVACY_POLICY');
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
		Schema::drop('company_terms');
	}
}
