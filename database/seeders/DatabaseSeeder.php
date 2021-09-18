<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(UsersTableSeeder::class);
        $this->call(InsertDataCategoriesTableSeed::class);
        $this->call(ServiceTableSeed::class);
        $this->call(RegionTableSeed::class);
        $this->call(RequestConsultingsTableSeeder::class);
        $this->call(FeeAndPeriodTableSeed::class);
        $this->call(AdvertisingBlockSeeder::class);
        $this->call(UpdateRegionTableSeeder::class);
        $this->call(UpdateDataCategoriesTableSeeder::class);
        $this->call(CompanyTermsTableSeed::class);
        $this->call(ReferralBonusTableSeed::class);
        $this->call(AnswerQuestionTableSeed::class);
    }
}
