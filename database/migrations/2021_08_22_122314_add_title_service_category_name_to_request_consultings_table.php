<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTitleServiceCategoryNameToRequestConsultingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('request_consultings', function (Blueprint $table) {
            $table->string('title_service_request')->nullable()->after('price_requested');
            $table->string('category_name_request')->nullable()->after('title_service_request');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('request_consultings', function (Blueprint $table) {
            $table->dropColumn('title_service_request');
            $table->dropColumn('category_name_request');
        });
    }
}
