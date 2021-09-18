<?php

namespace Database\Seeders;

use DB;
use Illuminate\Database\Seeder;

class CompanyTermsTableSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            [
                'file_name' => 'プライバシーポリシー.pdf',
                'file_path' => 'https://pwa-dev-storage.s3.ap-northeast-1.amazonaws.com/company_terms/original/1619684354.7623.pdf',
                'file_size' => '571788',
                'type' => 'PRIVACY_POLICY',
            ],
            [
                'file_name' => '利用規約.pdf',
                'file_path' => 'https://pwa-dev-storage.s3.ap-northeast-1.amazonaws.com/company_terms/original/1619684377.5374.pdf',
                'file_size' => '663634',
                'type' => 'TERMS_OF_USE',
            ],
            [
                'file_name' => '特定商取引に基づく表記 (1).pdf',
                'file_path' => 'https://pwa-dev-storage.s3.ap-northeast-1.amazonaws.com/company_terms/original/1619684424.3797.pdf',
                'file_size' => '543743',
                'type' => 'SYMBOL',
            ],
        ];

        DB::table('company_terms')->insert($data);
        $this->command->info('Inserted data company terms');
    }
}
