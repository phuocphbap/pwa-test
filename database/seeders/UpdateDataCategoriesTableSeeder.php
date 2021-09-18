<?php

namespace Database\Seeders;

use App\Entities\Category;
use DB;
use Illuminate\Database\Seeder;

class UpdateDataCategoriesTableSeeder extends Seeder
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
                'name' => '家事・庭',
                'description' => '',
                'cat_sort' => 0,
                'state' => 1,
            ],
            [
                'parent_id' => 0,
                'name' => 'Web制作',
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
                'name' => '各種相談',
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
                'name' => '美容・ファッション',
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
                'name' => '場所',
                'description' => '',
                'cat_sort' => 0,
                'state' => 1,
            ],
        ];
        Category::truncate();
        DB::statement('ALTER TABLE categories AUTO_INCREMENT = 0;');
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
        $this->command->info('Updated data categories');
    }
}
