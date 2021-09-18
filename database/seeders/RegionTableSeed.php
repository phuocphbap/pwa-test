<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Entities\Region;

class RegionTableSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $regionData = [
            [
                'country_code' => 'jp',
                'state_code' => '',
                'state_name' => '青森県',
                'type' => 'state',
            ],
            [
                'country_code' => 'jp',
                'state_code' => '',
                'state_name' => '神奈川県',
                'type' => 'state',
            ],
            [
                'country_code' => 'jp',
                'state_code' => '',
                'state_name' => '東京都',
                'type' => 'state',
            ],
            [
                'country_code' => 'jp',
                'state_code' => '',
                'state_name' => 'オンライン',
                'type' => 'state',
            ],
            [
                'country_code' => 'jp',
                'state_code' => '',
                'state_name' => '埼玉県',
                'type' => 'state',
            ],
            [
                'country_code' => 'jp',
                'state_code' => '',
                'state_name' => '北海道',
                'type' => 'state',
            ],
            [
                'country_code' => 'jp',
                'state_code' => '',
                'state_name' => '岩手県',
                'type' => 'state',
            ],
            [
                'country_code' => 'jp',
                'state_code' => '',
                'state_name' => '宮城県',
                'type' => 'state',
            ],
            [
                'country_code' => 'jp',
                'state_code' => '',
                'state_name' => '秋田県',
                'type' => 'state',
            ],
            [
                'country_code' => 'jp',
                'state_code' => '',
                'state_name' => '山形県',
                'type' => 'state',
            ],
            [
                'country_code' => 'jp',
                'state_code' => '',
                'state_name' => '福島県',
                'type' => 'state',
            ],
            [
                'country_code' => 'jp',
                'state_code' => '',
                'state_name' => '栃木県',
                'type' => 'state',
            ],
            [
                'country_code' => 'jp',
                'state_code' => '',
                'state_name' => '群馬県',
                'type' => 'state',
            ],
            [
                'country_code' => 'jp',
                'state_code' => '',
                'state_name' => '千葉県',
                'type' => 'state',
            ],
        ];

        //insert regions data
        foreach($regionData as $data) {
            $regionData = Region::create([
                'country_code' => $data['country_code'],
                'state_code' => $data['state_code'],
                'state_name' => $data['state_name'],
                'type' => $data['type'],
            ]);
        }
        $this->command->info('Inserted data Regions');
    }
}
