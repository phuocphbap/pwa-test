<?php

namespace Database\Seeders;

use DB;
use Faker\Generator as Faker;
use Illuminate\Database\Seeder;

class ReferralBonusTableSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(Faker $faker)
    {
        DB::table('referral_bonuses')->insert([
            'amount' => 300,
        ]);

        $this->command->info('Inserted data fee and period success');
    }
}
