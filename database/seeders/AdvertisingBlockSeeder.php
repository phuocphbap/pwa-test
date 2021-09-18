<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Entities\AdvertisingBlock;
use App\Entities\AdvertisingCategory;

class AdvertisingBlockSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $category = [
            [
                'id' => 1,
                'name' => 'みんなのサービス',
            ],
            [
                'id' => 2,
                'name' => 'おすすめ',
            ],
            [
                'id' => 3,
                'name' => 'みんなのストア',
            ],
            [
                'id' => 4,
                'name' => '近くのスポット',
            ],
        ];

        foreach ($category as $data) {
            AdvertisingCategory::create([
                'id' => $data['id'],
                'name' => $data['name'],
            ]);
        }

        $block = [
            [
                'category_id' => 1,
                'name' => 'ブロック１',
            ],
            [
                'category_id' => 1,
                'name' => 'ブロック２',
            ],
            [
                'category_id' => 1,
                'name' => 'ブロック３',
            ],
            [
                'category_id' => 2,
                'name' => 'ブロック１',
            ],
            [
                'category_id' => 2,
                'name' => 'ブロック２',
            ],
            [
                'category_id' => 2,
                'name' => 'ブロック３',
            ],
            [
                'category_id' => 3,
                'name' => 'ブロック１',
            ],
            [
                'category_id' => 3,
                'name' => 'ブロック２',
            ],
            [
                'category_id' => 4,
                'name' => 'ブロック１',
            ],
            [
                'category_id' => 4,
                'name' => 'ブロック２',
            ],
        ];

        foreach ($block as $data) {
            AdvertisingBlock::create([
                'category_id' => $data['category_id'],
                'name' => $data['name'],
            ]);
        }

    }
}
