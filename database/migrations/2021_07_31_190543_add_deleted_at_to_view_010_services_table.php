<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDeletedAtToView010ServicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('view_010_services', function (Blueprint $table) {
            DB::statement('DROP VIEW IF EXISTS view_010_services');
            DB::statement(
                'CREATE VIEW view_010_services AS
                SELECT services.id AS id,
                    services.category_id AS category_id,
                    services.store_id AS store_id,
                    services.service_image AS service_image,
                    services.service_title AS service_title,
                    services.service_detail AS service_detail,
                    services.time_required AS time_required,
                    services.price AS price,
                    services.is_blocked AS is_blocked,
                    services.sort AS sort,
                    services.reason_blocked AS reason_blocked,
                    services.time_sort AS time_sort,
                    users.user_name AS user_name,
                    users.id AS user_id,
                    users.avatar AS avatar,
                    users.state AS state,
                    services.deleted_at AS deleted_at
                FROM services
                INNER JOIN stores ON stores.id = services.store_id
                INNER JOIN users ON users.id = stores.user_id'
            );
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('view_010_services', function (Blueprint $table) {
            //
        });
    }
}
