<?php

use Illuminate\Database\Migrations\Migration;

class CreateView020Stores extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \DB::statement('CREATE VIEW view_020_stores AS
            SELECT
                stores.id AS id,
                stores.store_address AS store_address,
                stores.latitude AS latitude,
                stores.longitude AS longitude,
                stores.image_map AS store_image_map,
                users.email AS email,
                users.user_name AS user_name,
                users.id AS user_id,
                users.avatar AS avatar,
                users.first_name AS first_name,
                users.last_name AS last_name,
                users.gender AS gender,
                users.detail AS detail,
                users.address_id AS address_id,
                users.phone AS phone,
                users.birth_date AS birth_date,
                users.referral_code AS referral_code,
                users.ranking AS ranking,
                users.state AS state,
                (SELECT COUNT(*) AS sum
                FROM `request_consultings`
                INNER JOIN services ON services.id = request_consultings.service_id
                WHERE store_id =stores.id AND `request_consultings`.progress = 4) AS agreements_count
            FROM stores
            INNER JOIN users ON stores.user_id = users.id'
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('DROP VIEW view_020_stores');
    }
}
