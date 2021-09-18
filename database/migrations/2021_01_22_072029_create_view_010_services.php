<?php

use Illuminate\Database\Migrations\Migration;

class CreateView010Services extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \DB::statement('CREATE VIEW view_010_services AS
            SELECT
                services.id AS id,
                services.category_id AS category_id,
                services.store_id AS store_id,
                services.region_id AS region_id,
                services.service_image AS service_image,
                services.service_title AS service_title,
                services.service_detail AS service_detail,
                services.time_required AS time_required,
                services.price AS price,
                users.user_name AS user_name,
                users.id AS user_id,
                users.avatar AS avatar,
                users.state AS state
            FROM services
            INNER JOIN stores ON stores.id = services.store_id
            INNER JOIN users ON users.id = stores.user_id'
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        \DB::statement('DROP VIEW view_010_services');
    }
}
