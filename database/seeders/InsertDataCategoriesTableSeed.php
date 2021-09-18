<?php

namespace Database\Seeders;

use App\Entities\Category;
use Illuminate\Database\Seeder;

class InsertDataCategoriesTableSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $dataCategories = [
            //parent categories
            [
                'parent_id' => 0,
                'name' => '家事',
                'description' => '',
                'cat_sort' => 0,
                'state' => 1,
            ],
            [
                'parent_id' => 0,
                'name' => '修理・組立',
                'description' => '',
                'cat_sort' => 0,
                'state' => 1,
            ],
            [
                'parent_id' => 0,
                'name' => 'ペット',
                'description' => '',
                'cat_sort' => 0,
                'state' => 1,
            ],
            [
                'parent_id' => 0,
                'name' => '高齢者',
                'description' => '',
                'cat_sort' => 0,
                'state' => 1,
            ],
            [
                'parent_id' => 0,
                'name' => '趣味・習い事',
                'description' => '',
                'cat_sort' => 0,
                'state' => 1,
            ],
            [
                'parent_id' => 0,
                'name' => 'その他',
                'description' => '',
                'cat_sort' => 0,
                'state' => 1,
            ],
            [
                'parent_id' => 0,
                'name' => '生活相談',
                'description' => '',
                'cat_sort' => 0,
                'state' => 1,
            ],
            [
                'parent_id' => 0,
                'name' => '料理',
                'description' => '',
                'cat_sort' => 0,
                'state' => 1,
            ],
            [
                'parent_id' => 0,
                'name' => '美容・ファッション',
                'description' => '',
                'cat_sort' => 0,
                'state' => 1,
            ],
            [
                'parent_id' => 0,
                'name' => '写真・動画制作',
                'description' => '',
                'cat_sort' => 0,
                'state' => 1,
            ],
            [
                'parent_id' => 0,
                'name' => 'インテリア',
                'description' => '',
                'cat_sort' => 0,
                'state' => 1,
            ],
            [
                'parent_id' => 1,
                'name' => '買い物代行',
                'description' => '',
                'cat_sort' => 0,
                'state' => 1,
            ],
            [
                'parent_id' => 1,
                'name' => 'クリーニング',
                'description' => '',
                'cat_sort' => 0,
                'state' => 1,
            ],
            [
                'parent_id' => 1,
                'name' => '害虫駆除',
                'description' => '',
                'cat_sort' => 0,
                'state' => 1,
            ],
            [
                'parent_id' => 1,
                'name' => 'エアコン掃除',
                'description' => '',
                'cat_sort' => 0,
                'state' => 1,
            ],
            [
                'parent_id' => 1,
                'name' => '外壁掃除',
                'description' => '',
                'cat_sort' => 0,
                'state' => 1,
            ],
            [
                'parent_id' => 1,
                'name' => '運転代行',
                'description' => '',
                'cat_sort' => 0,
                'state' => 1,
            ],
            [
                'parent_id' => 1,
                'name' => '整理整頓',
                'description' => '',
                'cat_sort' => 0,
                'state' => 1,
            ],
        ];

        //insert categories data
        foreach ($dataCategories as $data) {
            $dataCats = Category::create([
                'parent_id' => $data['parent_id'],
                'name' => $data['name'],
                'description' => $data['description'],
                'cat_sort' => $data['cat_sort'],
                'state' => $data['state'],
            ]);
        }
        $this->command->info('Inserted data categories');
    }
}
