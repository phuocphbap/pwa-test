<?php

namespace Database\Seeders;

use App\Entities\Store;
use App\Entities\Wallet;
use DB;
use Faker\Generator as Faker;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(Faker $faker)
    {
        for ($i = 1; $i <= 3; ++$i) {
            $email = 'admin'.$i.'@gmail.com';
            $password = Hash::make('12341234');
            // $role = 1;

            DB::table('users')->insert([
                'email' => $email,
                'avatar' => 'https://lh3.googleusercontent.com/myjQ9vlfJQOwpytHPvCow1gwcNvJXVs0Nfo7Cf6n7WvwUsu8y2zQvcjh6Bwk47TiCMkF43ZVMWKi0GKBX180Zb3vbk06Fg=s250',
                'user_name' => 'わかにゃん',
                'password' => $password,
                'first_name' => $faker->firstName,
                'last_name' => $faker->lastName,
                'gender' => $faker->randomElement([0, 1]),
                'detail' => $faker->text,
                'phone' => $faker->phoneNumber,
                'is_phone_verified' => 0,
                'is_email_verified' => 1,
                'birth_date' => $faker->date,
                'referral_code' => $faker->postcode,
                'ranking' => 0,
                'reason_leave' => $faker->text,
                'state' => 1,
                'email_verified_at' => now(),
                'remember_token' => Str::random(10),
            ]);
            $dataWallet = Wallet::create([
                'user_id' => $i,
                'amount' => 0,
                'state' => 1,
            ]);
            $dataStore = Store::create([
                'user_id' => $i,
                'store_address' => '',
                'latitude' => '',
                'longitude' => '',
                'state' => 1,
            ]);
        }

        $this->command->info('Inserted data user!');
    }
}
