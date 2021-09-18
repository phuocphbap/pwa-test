<?php

namespace Database\Seeders;

use DB;
use Illuminate\Database\Seeder;

class AnswerQuestionTableSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $dataAnswerQuestion = [
            [
                'topic_id' => 1,
                'order' => 1,
                'question' => 'サービスをリクエストしたい',
                'answer' => 'サービスをリクエストしたい場合は、受けたいサービスを見つけたり作成したりできますリクエスト。',
                'state' => 1,
            ],
            [
                'topic_id' => 1,
                'order' => 1,
                'question' => 'お支払いに関する問題',
                'answer' => 'この契約は、事前に料金を支払うことによって行われます。前払いは、作業完了後にサポーターに支払われます。',
                'state' => 1,
            ],
            [
                'topic_id' => 2,
                'order' => 1,
                'question' => '申し込みの受付方法',
                'answer' => 'リクエストの申し込みがあります。その中から、内容、日時、料金に一致するものを選択し、前払い手続きに進んでください。',
                'state' => 1,
            ],
            [
                'topic_id' => 3,
                'order' => 1,
                'question' => 'トランザクションが完了したとき',
                'answer' => 'サービス提供後、お互いを評価し、取引を完了します。その後、サポーターは報酬を受け取ります。',
                'state' => 1,
            ],
        ];

        $dataTopicQuestion = [
            [
                'title' => '初心者のためのチュートリアル。',
            ],
            [
                'title' => 'プロバイダーへの指示',
            ],
            [
                'title' => 'サービスプロバイダー向けの説明',
            ],
        ];

        DB::table('answer_questions')->insert($dataAnswerQuestion);
        DB::table('topic_questions')->insert($dataTopicQuestion);
        $this->command->info('Inserted data answer questions and topic questions');
    }
}
