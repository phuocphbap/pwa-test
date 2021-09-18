<?php

namespace Database\Seeders;

use App\Entities\RequestConsulting;
use Illuminate\Database\Seeder;

class RequestConsultingsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $dataRequests = [
            [
                'customer_id' => 2,
                'owner_id' => 1,
                'service_id' => 1,
                'message' => 'すぐにサービスが必要です。ありがとう',
                'progress' => 0,
            ],
            [
                'customer_id' => 3,
                'owner_id' => 1,
                'service_id' => 1,
                'message' => 'すぐにサービスが必要です。ありがとう',
                'progress' => 1,
            ],
        ];
        //insert requests data
        foreach ($dataRequests as $data) {
            $dataRequest = RequestConsulting::create([
                'customer_id' => $data['customer_id'],
                'owner_id' => $data['owner_id'],
                'service_id' => $data['service_id'],
                'message' => $data['message'],
                'progress' => $data['progress'],
            ]);
        }
        $this->command->info('Inserted data Request');
    }
}
