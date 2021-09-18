<?php

use Illuminate\Database\Migrations\Migration;

class UpdateView020Stores extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \DB::statement('DROP VIEW view_020_stores');
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
                users.is_phone_verified AS is_phone_verified,
                users.is_email_verified AS is_email_verified,
                users.is_kyc_profiled AS is_kyc_profiled,
                users.identity_status AS identity_status,
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
    }
}
