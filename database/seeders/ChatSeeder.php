<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Entities\Chat;
use Faker\Generator as Faker;

class ChatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(Faker $faker)
    {
        $chats = [
            [
                'key_firebase' => '-MSC-qYNHsX3-RzJ_9kk',
                'room_name' => 1,
                'user_agent' => 1,
                'user_forward' => 2,
                'state' => 1,
            ],
            [
                'key_firebase' => '-MSC-SYNHeX3-RzJ_9kk',
                'room_name' => 2,
                'user_agent' => 1,
                'user_forward' => 3,
                'state' => 1,
            ],
            [
                'key_firebase' => '-MSC-SYNdsX3-RzJ_9kk',
                'room_name' => 3,
                'user_agent' => 2,
                'user_forward' => 3,
                'state' => 1,
            ],
        ];

        foreach ($chats as $data) {
            $chat = Chat::create([
                'key_firebase' => $data['key_firebase'],
                'room_name' => $data['room_name'],
                'user_agent' => $data['user_agent'],
                'user_forward' => $data['user_forward'],
                'state' => $data['state'],
            ]);
        }
        $this->command->info('Inserted data Chats');
    }
}
