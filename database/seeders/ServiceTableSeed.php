<?php

namespace Database\Seeders;

use App\Entities\Service;
use Illuminate\Database\Seeder;

class ServiceTableSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $dataServices = [
            [
                'category_id' => 1,
                'store_id' => 1,
                'region_id' => 1,
                'service_image' => 'https://lh3.googleusercontent.com/MSwDWEULzC8Z00F6cCLwtdBHaIN4dyvDlLSGkBorgY2uorVjmZPELV8RNTBqrDc3QL6Pc0XPahEJ9zyUpZ-0AuM5wDw=s800',
                'service_title' => '女性限定）お掃除、お洗濯させていただきます',
                'service_detail' => 'どんなに散らかったお部屋でも安心してご相談ください。
                ・ご自宅にあるお掃除道具を使用させていただきます。ゴミ袋など必要なものはご用意ください。
                ・申し訳ありませんが、当方初心者につきしばらくの間は女性に限定させていただきます。
                ・掃除、洗濯以外もご相談ください。（アイロンがけ、食器洗いなど）
                ・交通費は別途頂戴します。（ガソリン代　1㎞10円目安）
                ・1時間1000円〜お時間はご相談に応じます。',
                'price' => 1000,
                'time_required' => 1,
            ],
            [
                'category_id' => 1,
                'store_id' => 2,
                'region_id' => 1,
                'service_image' => 'https://lh3.googleusercontent.com/oXHVIkFu_Brk6zv_kUPqa9H4ECwAvo8Q8YhyvGcNHULGSCtEVJEFnBIhiMguK3A0FHqEMm5tSIIsTn9wjQKMiJxXpZFNdw=s800',
                'service_title' => '真面目で誠実なサービスいたします♪家事代行サービス、お掃除',
                'service_detail' => 'ご覧いただきありがとうございます。
                現在家事代行の仕事をしています。
                
                忙しくてお掃除に手が回らない、少しでも家事の手助けが欲しいなど忙しいあなたに代わって家事のお手伝いができればと思います。
                どうぞお気軽にお問い合わせください。
                
                対応エリア：鹿児島市天文館近辺、中央駅近辺
                
                家事代行
                ・食器洗い
                ・拭き掃除
                ・掃除機かけ
                ・水回りのお掃除
                ・シーツ交換
                ・洗濯
                ・庭掃除
                ・花の水やり
                など
                
                家事の範囲のサービス提供となります
                調理も可能です。
                
                基本、ご依頼主様宅のお掃除用具を使用させていただきます。
                掃除機、スポンジ、モップなど
                
                詳細はお問い合わせください。',
                'price' => 4000,
                'time_required' => 2,
            ],
            [
                'category_id' => 2,
                'store_id' => 3,
                'region_id' => 2,
                'service_image' => 'https://lh3.googleusercontent.com/oXHVIkFu_Brk6zv_kUPqa9H4ECwAvo8Q8YhyvGcNHULGSCtEVJEFnBIhiMguK3A0FHqEMm5tSIIsTn9wjQKMiJxXpZFNdw=s800',
                'service_title' => '年内空き僅か！お掃除・片付けなど★東京・神奈川・千葉・埼玉対応！',
                'service_detail' => '★女性指名可能です★
                    ご自宅にある道具をお借りして代わりにお掃除いたします！
                    ・掃除、片付けが苦手な方
                    ・仕事や育児で忙しい方
                    ・ケガでうまく動けない・・・などなど
                    ※交通費別途頂戴します。
                    ※最低2ｈ～ご依頼承ります。
                    ※2.5ｈ以上からは1625円/30分で延長ご相談承ります。
                    
                    ＜例えば2ｈでこんなお掃除＞
                    お部屋の掃除機掛け
                    トイレ掃除
                    お風呂掃除
                    台所のお掃除
                    洗濯物畳み
                    ゴミまとめ
                    
                    ※汚れの状態や、お部屋の広さなどによって必要な時間も前後しますので、最初にいろいろとご質問させていただきます！',
                'price' => 6500,
                'time_required' => 3,
            ],
            [
                'category_id' => 3,
                'store_id' => 1,
                'region_id' => 1,
                'service_image' => 'https://storage.googleapis.com/static.anytimes.jp/images/categories/191.jpg',
                'service_title' => '手作りおやつ、お料理、その他日常家事代行します！',
                'service_detail' => 'ご覧頂きありがとうございますm(._.)m

                    ◎お仕事の合間につまみたいおやつ、来客用にお茶菓子を用意したい、毎日のおやつに無添加お菓子作り置き代行
                    
                    ◎家にある材料をできるだけ使って食材をムダにしないお料理作り、たまには手料理が食べたい方へ料理代行
                    
                    ◎買い物代行、日常家事、整理整頓、その他の代行業務
                    
                    ◎お仕事サポート等
                    
                    【交通費別途】
                    東京都 品川区、目黒区、渋谷区、新宿区、千代田区、港区、中央区 ￥600
                    東京都23区内 ￥1000
                    他地区はご相談ください。
                    
                    内容詳細、時間、金額などお気軽にご相談ください(^-^)',
                'price' => 4500,
                'time_required' => 3,
            ],
            [
                'category_id' => 4,
                'store_id' => 2,
                'region_id' => 2,
                'service_image' => 'https://storage.googleapis.com/static.anytimes.jp/images/categories/192.jpg',
                'service_title' => 'お食事の調理代行致します！',
                'service_detail' => '身体に優しい栄養バランスの取れるお食事の作り置きのお手伝い致します。
                    お仕事で忙しく外食になりがちな1人暮らしの方や
                    小さなお子様がいて調理が難しいお母さん、働く女性の方々、ご年配の方で毎日に料理は面倒という方のお手伝いをさせて頂きます。
                    普段は個人宅での調理代行や家事代行を3年程させて頂いておりました。お食事はご希望をお聞きして献立の方をご提案させて頂きます。',
                'price' => 6000,
                'time_required' => 5,
            ],
            [
                'category_id' => 5,
                'store_id' => 3,
                'region_id' => 2,
                'service_image' => 'https://lh3.googleusercontent.com/UbqxtBUyfaMbhfHbPRjpJiZHJX5aPm_cIyOsPqC8GA3mlhp0qFhlvps1AraSsiynNJcPsCo2ViRC6WWLdg83BCVfRGuC=s800',
                'service_title' => '★料理代行★作り置きで家事の負担を軽減しませんか？',
                'service_detail' => '最近人気の作り置きで、食生活を見直してみませんか？
                    ・忙しくて外食の多い方
                    ・妊娠中で食事に気を付けたい方
                    ・たまには誰かが作ったものを食べたい方
                    ・家事を休みたい方
                    ※ご依頼は最低2ｈ～承ります。
                    ※お買物からご依頼される場合は2.5ｈ～承ります。買い物をする場所とご自宅が徒歩圏内である場合に限り買い物から対応可能です。
                    
                    好き嫌いやアレルギーなどがあればお気軽に事前にお申し付けください。
                    ある程度食材をご用意しておいてくだされば
                    その場で相談して、ぱぱっとお作りすることも可能です(^^♪
                    味の好みなども確認していただけますので、ご安心ください。',
                'price' => 3500,
                'time_required' => 5,
            ],
            [
                'category_id' => 6,
                'store_id' => 1,
                'region_id' => 4,
                'service_image' => 'https://storage.googleapis.com/static.anytimes.jp/images/categories/24.jpg',
                'service_title' => '照明器具・ＴＶ・スイッチ・コンセント・インターホン交換',
                'service_detail' => 'オシャレな照明器具買っても取り付け出来ない
                    ＴＶを壁掛にしたい
                    コンセントにひびが入った
                    インターホンが調子悪い
                    など困ったらお知らせください
                    
                    見積りを早くするには
                    1.商品は有・無(無い場合購入依頼有・無)メーカー型番又は写真(交換予定の現在ついている器具と新品の器具)
                    2.住所(○○線○○駅)',
                'price' => 4500,
                'time_required' => 3,
            ],
            [
                'category_id' => 7,
                'store_id' => 1,
                'region_id' => 5,
                'service_image' => 'https://lh3.googleusercontent.com/wX39nN20moTWxknDKpvGOGWYrp0vye4aBh9x3SR1z4nbet0GTQ92Yyczern5wnZ1pZSYua9BPp9b7h8f3a_ZdX6BQC7Z6nhjKoNp_Gha=s800',
                'service_title' => 'IKEA家具のプロが組み立てします',
                'service_detail' => '-------------------------------------------------------
                    IKEA/イケア家具組み立てトレーニング受講済みです。
                    -------------------------------------------------------
                    
                    IKEAで購入された家具の組み立てのお手伝いいたします。
                    少し長くなりますが、お互いに安心してサービスの利用ができるように最後までお読みください。
                    
                    
                    【サポーターについて】
                    大阪初のIKEA家具組み立て認定サポーターです。
                    ソファ、ベッド、リビング収納、ワードローブ収納、ダイニング、デスク等IKEAの家具は大型家具含め一通り組み立て経験があります。
                    基本的に電車でのお伺いになります。
                    組み立てに必要な工具類は持参します。
                    引越し前の解体、引越し後の再組み立ても、商品によっては可能なのでご相談ください。',
                'price' => 5500,
                'time_required' => 3,
            ],
            [
                'category_id' => 9,
                'store_id' => 1,
                'region_id' => 6,
                'service_image' => 'https://lh3.googleusercontent.com/lPL5wnCl_KMxXZpA6cgRI4wjQJzllfok0zZS4wl7P46MhHGeGAhWGhjYekRJjvfl9XgzpDx_41TFjXpoNbY5MH6f8sOB-1kE6UoOnr3a=s800',
                'service_title' => 'IKEA】IKEA家具組み立てお手伝いします！',
                'service_detail' => 'イケアで購入された家具の組み立てをお手伝いいたします。

                    収納棚などの小中規模家具やソファ、ベッド、ワードローブのような大きな家具など組み立て経験ございます。
                    
                    <対応エリア>
                    東京を中心に、神奈川県、埼玉県、千葉県など。
                    
                    <料金>
                    小さな家具で3,500円～となります。
                    ご予算や家具の種類や伺う場所などを基に都度ご相談しながら決めていければと考えています。',
                'price' => 500,
                'time_required' => 3,
            ],
            [
                'category_id' => 10,
                'store_id' => 1,
                'region_id' => 7,
                'service_image' => 'https://lh3.googleusercontent.com/Yvp1FHG-oboGsaWOijcK0JcNzPfHgbdV_BoMy1BULR39dXN3YXxBUy01t-Zip0dhTMbfPItYi3YnjiiCgr-USjQhS9hWfw=s800',
                'service_title' => 'たった1記事で89万を売ったコピーの秘密を教えます【PDF納品】',
                'service_detail' => 'ブログ初心者だった私が、
                    たった1記事で891,942円を生み出したライティングノウハウを知りたいですか？
                    
                    
                    多くの方がブログでお金を稼ごうと思っているにも関わらず、収益が１円も上がらないという悲劇に見舞われています。
                    
                    アドセンスを利用したトレンドブログであれば、
                    アクセスを集めることで収益を発生させることができるのですが、ASP案件などをアフィリエイトする場合はアクセスを集めてもライティングテクニックがないと利益を出すことができません。
                    
                    
                    私自身、トレンドブログで稼いでいましたが
                    正直、毎日ブログの記事を書くのが辛くなったので、アフィリエイトも同時並行してチャンレンジしていました！',
                'price' => 1900,
                'time_required' => 3,
            ],
            [
                'category_id' => 11,
                'store_id' => 1,
                'region_id' => 4,
                'service_image' => 'https://lh3.googleusercontent.com/AQvJwL-n3v_9hbuHfA88FNuV2ctPMQ67PrYzMyOo587ID8kW0X4W4NnC1XUGCpxcoWY6P4byi-NqNq_qCk3v92CpR0igcg=s800',
                'service_title' => '木部（建具枠やフローリング）の傷補修',
                'service_detail' => '新築やリノベーション物件、ご自宅の床やドアなどお部屋の傷補修、ハウスリペアを副業でやっております。

                    ものを落としてしまったり、ぶつけてしまってできた凹みやめくれ等の傷を専門の技術で補修いたします。
                    
                    ■対応日時
                    現場へお伺いできるのは日曜日のみ、傷は3箇所まで補修時間は2時間までとさせて頂きたいと思います。
                    
                    ■対応エリア
                    東京、神奈川、埼玉、千葉になります。
                    
                    ■料金
                    自分で仕事を請負って行うのは初めてになりますので、まずは経験させて頂きたいと思い無料で行います。',
                'price' => 0,
                'time_required' => 3,
            ],
            [
                'category_id' => 12,
                'store_id' => 1,
                'region_id' => 7,
                'service_image' => 'https://storage.googleapis.com/static.anytimes.jp/images/categories/68-or.jpg',
                'service_title' => 'モーニングコール',
                'service_detail' => 'モーニングコールを実施します！

                    朝でなくとも、日中・夜など、お時間はご相談可能です。
                    
                    ご料金は1回あたりを想定、モーニングコールと合わせて、ご希望があれば3分程度の雑談もさせてください＾＾',
                'price' => 200,
                'time_required' => 3,
            ],
        ];
        //insert service data
        foreach ($dataServices as $data) {
            $dataServices = Service::create([
                'category_id' => $data['category_id'],
                'store_id' => $data['store_id'],
                'region_id' => $data['region_id'],
                'service_image' => $data['service_image'],
                'service_title' => $data['service_title'],
                'service_detail' => $data['service_detail'],
                'price' => $data['price'],
                'time_required' => $data['time_required'],
            ]);
        }
        $this->command->info('Inserted data Services');
    }
}
