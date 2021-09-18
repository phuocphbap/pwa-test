<?php

namespace Database\Seeders;

use App\Entities\FeePayment;
use App\Entities\PeriodExpire;
use Faker\Generator as Faker;
use Illuminate\Database\Seeder;

class FeeAndPeriodTableSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(Faker $faker)
    {
        $fee = FeePayment::create([
            'fee_percent' => 15,
        ]);
        $fee = PeriodExpire::create([
            'sum_month' => 12,
        ]);

        $this->command->info('Inserted data fee and period success');
    }
}
