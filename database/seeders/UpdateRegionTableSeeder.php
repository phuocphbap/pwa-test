<?php

namespace Database\Seeders;

use App\Entities\Region;
use DB;
use Illuminate\Database\Seeder;

class UpdateRegionTableSeeder extends Seeder
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
                'state_name' => '津市',
                'type' => 'state',
            ],
            [
                'country_code' => 'jp',
                'state_code' => '',
                'state_name' => '四日市市',
                'type' => 'state',
            ],
            [
                'country_code' => 'jp',
                'state_code' => '',
                'state_name' => '伊勢市',
                'type' => 'state',
            ],
            [
                'country_code' => 'jp',
                'state_code' => '',
                'state_name' => '松阪市',
                'type' => 'state',
            ],
            [
                'country_code' => 'jp',
                'state_code' => '',
                'state_name' => '桑名市',
                'type' => 'state',
            ],
            [
                'country_code' => 'jp',
                'state_code' => '',
                'state_name' => '鈴鹿市',
                'type' => 'state',
            ],
            [
                'country_code' => 'jp',
                'state_code' => '',
                'state_name' => '名張市',
                'type' => 'state',
            ],
            [
                'country_code' => 'jp',
                'state_code' => '',
                'state_name' => '尾鷲市',
                'type' => 'state',
            ],
            [
                'country_code' => 'jp',
                'state_code' => '',
                'state_name' => '亀山市',
                'type' => 'state',
            ],
            [
                'country_code' => 'jp',
                'state_code' => '',
                'state_name' => '鳥羽市',
                'type' => 'state',
            ],
            [
                'country_code' => 'jp',
                'state_code' => '',
                'state_name' => '熊野市',
                'type' => 'state',
            ],
            [
                'country_code' => 'jp',
                'state_code' => '',
                'state_name' => 'いなべ市',
                'type' => 'state',
            ],
            [
                'country_code' => 'jp',
                'state_code' => '',
                'state_name' => '志摩市',
                'type' => 'state',
            ],
            [
                'country_code' => 'jp',
                'state_code' => '',
                'state_name' => '伊賀市',
                'type' => 'state',
            ],
            [
                'country_code' => 'jp',
                'state_code' => '',
                'state_name' => '木曽岬町',
                'type' => 'state',
            ],
            [
                'country_code' => 'jp',
                'state_code' => '',
                'state_name' => '東員町',
                'type' => 'state',
            ],
            [
                'country_code' => 'jp',
                'state_code' => '',
                'state_name' => '菰野町',
                'type' => 'state',
            ],
            [
                'country_code' => 'jp',
                'state_code' => '',
                'state_name' => '朝日町',
                'type' => 'state',
            ],
            [
                'country_code' => 'jp',
                'state_code' => '',
                'state_name' => '川越町',
                'type' => 'state',
            ],
            [
                'country_code' => 'jp',
                'state_code' => '',
                'state_name' => '多気町',
                'type' => 'state',
            ],
            [
                'country_code' => 'jp',
                'state_code' => '',
                'state_name' => '明和町',
                'type' => 'state',
            ],
            [
                'country_code' => 'jp',
                'state_code' => '',
                'state_name' => '大台町',
                'type' => 'state',
            ],
            [
                'country_code' => 'jp',
                'state_code' => '',
                'state_name' => '玉城町',
                'type' => 'state',
            ],
            [
                'country_code' => 'jp',
                'state_code' => '',
                'state_name' => '度会町',
                'type' => 'state',
            ],
            [
                'country_code' => 'jp',
                'state_code' => '',
                'state_name' => '大紀町',
                'type' => 'state',
            ],
            [
                'country_code' => 'jp',
                'state_code' => '',
                'state_name' => '南伊勢町',
                'type' => 'state',
            ],
            [
                'country_code' => 'jp',
                'state_code' => '',
                'state_name' => '紀北町',
                'type' => 'state',
            ],
            [
                'country_code' => 'jp',
                'state_code' => '',
                'state_name' => '御浜町',
                'type' => 'state',
            ],
            [
                'country_code' => 'jp',
                'state_code' => '',
                'state_name' => '紀宝町',
                'type' => 'state',
            ],
        ];

        Region::truncate();
        DB::statement('ALTER TABLE regions AUTO_INCREMENT = 0;');
        //insert regions data
        foreach ($regionData as $data) {
            $regionData = Region::create([
                'country_code' => $data['country_code'],
                'state_code' => $data['state_code'],
                'state_name' => $data['state_name'],
                'type' => $data['type'],
            ]);
        }
        $this->command->info('Updated data Regions');
    }
}
