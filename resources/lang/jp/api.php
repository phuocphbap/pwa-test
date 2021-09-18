<?php

return [
    'common' => [
        'search' => '検索',
        'status' => 'ステータス',
        'send' => '送信',
        'create_success' => '作成が完了しました。',
        'update_success' => '更新が完了しました。',
        'close' => '閉じる',
        'save' => '保存',
        'filter_btn' => '絞り込み',
        'filter_reset' => 'リセット',
        'list_title' => 'リスト',
        'add_btn' => '追加',
        'edit_btn' => '編集',
        'delete_btn' => '削除',
        'active' => 'アクティブ',
        'inactive' => '非アクティブ',
        'save_btn' => '保存',
        'cancel_btn' => 'キャンセル',
        'all' => 'すべて',
        'create' => '作成',
        'update' => '更新',
        'total' => '合計',
        'action' => '操作',
        'export_excel' => 'エクセルの出力',
        'show_hide' => '表示/非表示',
        'result' => '結果',
        'page' => 'ページ',
        'id' => 'Id',
        'show' => '表示',
        'hide' => '非表示',
        'next' => '次へ',
        'previous' => '前へ',
        'success' => '成功しました。',
        'failed' => '失敗しました。',
        'deleted' => '削除しました。',
        'ok' => 'OK',
        'cancel' => 'キャンセル',
        'clear' => 'クリアー',
        'process_error' => '進捗にエラーが発生しました。',
        'published' => '公開',
        'unpublished' => '非公開',
        'all_category' => 'すべてのカテゴリー',
        'not_found' => '見つかりませんでした。',
        'id_not_exists' => 'データが存在しません。',
        'start_date_format' => '開始日は日付の形式で入力してください。',
        'end_date_format' => '終了日は日付の形式で入力してください。',
        'per_page_number' => 'ページネーションは数値で入力してください。',
        'id_required' => 'IDは必須です。',
        'id_array' => 'IDは配列である必要があります。',
        'you_not_permission' => '許可されていません。',
    ],
    'login' => [
        'login_fail' => 'メールアドレスまたはパスワードが正しくありません。',
        'login_success' => 'ログインに成功しました。',
        'login_password' => 'パスワードを入力してください。',
        'login_email' => 'メールアドレスを入力してください。',
        'email_not_verified' => 'メールアドレスはまだ認証されていません。',
        'account_not_exists' => 'このメールアドレスでアカウントがまだ登録されていません。',
        'user_invalid' => 'ユーザーはブラックリストに登録されましたか、自分でアカウントをロックしてしまいました。',
        'logout_success' => 'ログアウトに成功しました。',
        'otp_invalid' => '入力された承認コードは間違っています。もう一度実行してください。',
        'otp_expire' => 'OTPの有効期限が切れました',
        'resend_otp_success' => 'OTPコードが正常に再送されました。',
    ],
    'reset_password' => [
        'not_found' => 'メールが登録されていません。',
        'sent_success' => '指定のメールアドレスにパスワードをリセットするためのリンクを含むメールを送信しました。',
        'sent_failed' => 'パスワードの再設定メールを送信する時のエラーが発生しました。',
        'token_invalid' => 'パスワードリセットトークンは無効です。',
        'token_expire' => 'このパスワードリセット用トークンの有効期限が切れてしまいました。',
        'reset_password_success' => 'パスワードを正常にリセットされました。',
        'invalid_user' => 'ユーザーが無効です。',
        'invalid_email' => 'メールアドレスは登録済みのメールアドレスと同じくありません。',
        'email_not_confirm' => 'メールは確認されていません。確認のためにあなたの電子メールをチェックしてください。',
    ],
    'verify_email' => [
        'sent_success' => '指定のメールアドレスにパスワードをリセットするためのリンクを含むメールを送信しました。',
        'token_invalid' => 'トークンは無効です。',
        'token_expire' => '認証コードの有効期限が切れてしまいました。',
    ],
    'user' => [
        'email_required' => 'メールアドレスを入力してください。',
        'email_email' => 'メールアドレスの形式が正しくありません。',
        'password_required' => 'パスワードを入力してください。',
        'password_min' => 'パスワードは8文字以上で入力してください。',
        'password_confirmation_required' => '確認用パスワードを入力してください。',
        'password_confirmation_same' => '確認用パスワードが正しくありません。',
        'password_incorect' => 'パスワードが間違っています。',
        'email' => 'メールアドレス',
        'phone' => '電話番号',
        'username' => 'ユーザー名',
        'password' => 'パスワード',
        'confirm_password' => '確認用パスワード',
        'phone_min' => '電話番号は8桁以上で入力してください。',
        'phone_max' => '電話番号は15桁以内で入力してください。',
        'phone_regex' => '電話番号の形式が正しくありません。',
        'email_required' => 'メールアドレスを入力してください。',
        'email_unique' => 'このメールアドレスは既に登録されています。',
        'user_name_required' => 'ユーザー名は必須です。',
        'gender_required' => '性別は必須です。',
        'address_id_required' => '地域は必須です。',
        'birth_date_required' => '年月日は必須です。',
        'birth_date_invalid' => '年月日の形式が正しくありません。',
        'age_invalid' => '18歳以上でなければ、アカウントが登録できません。',
        'first_name_required' => '名は必須です。',
        'last_name_required' => '姓は必須です。',
        'account_updated' => '情報を更新しました。',
        'latitude_required' => '店舗の住所が現在地の場合、緯度は必須です。',
        'longitude_required' => '店舗の住所が現在地の場合、軽度は必須です。',
        'phone_required' => '電話番号を入力してください。',
        'latitude_number' => '緯度は数値で入力してください。',
        'longitude_number' => '軽度は数値で入力してください。',
        'avatar_mimes' => 'アバター画像は、jpeg、png、jpg、gif、svgファイル形式でアップロードしてください。',
        'avatar_max' => 'アバターは5120キロバイト以下でアップロードしてください。',
        'input_refferal_code_exists' => '選択された紹介コードが無効です。',
    ],
    'validation' => [
        'services_id_required' => 'サービスは必須です。',
        'services_not_exists' => 'サービスIDが存在しません。',
        'user_owner_invalid' => 'ユーザーIDが無効です。',
        'user_owner_required' => 'ユーザーIDは必須です',
        'room_data_has_been_deleted' => 'ルームデータが削除されました。',
        'progress_not_yet_finish' => '進捗はまだ終了ていません。',
        'services_is_selling' => 'サービスが販売されています。',
        'user_is_pending_withdraw' => '出金処理待ちの状態となります。',
        'user_id_required' => 'ユーザーIDは必須です。',
        'user_id_not_exits' => 'ユーザーIDが存在しません。',
        'user_id_array' => 'ユーザーIDは配列である必要があります。',
        'type_required' => 'タイプは必須です。',
        'type_invalid' => 'タイプが無効です。',
        'room_key_required' => 'ルームキーは必須です。',
        'room_key_exists' => 'ルームキーが存在しません。',
        'step_required' => 'ステップは必須です。',
        'step_numeric' => 'ステップは数字で入力してください。',
        'step_in' => 'ステップが無効です。',
        'price_required' => '金額は必須です。',
        'name_owner_required' => '所有者名は必須です。',
        'name_customer_required' => '顧客名は必須です。',
        'name_services_required' => 'サービス名は必須です。',
        'consulting_id_required' => '依頼相談IDは必須です。',
        'consulting_id_exists' => '依頼相談IDが存在しません。',
        'text_required' => 'テキストは必須です。',
        'room_key_required' => 'ルームキーは必須です。',
        'room_key_required' => 'ルームキーは必須です。',
        'file_name_mimetypes' => 'ファイル名は.pdf形式である必要があります。',
    ],
    'payment' => [
        'amount_required' => '金額は必須です。',
        'amount_invalid' => '金額が無効です。',
        'point_not_enough' => 'ポイントが足りません。',
        'valid_point' => 'ポイントの使用が有効です。',
        'error_create_intent' => 'お支払い作成時のエラーが発生しました。（最低金額の50円）',
        'failed' => 'お支払いが失敗しました。',
        'success' => 'お支払いが成功しました。',
        'used_coupon' => 'クーポンはすでに使用されました。',
        'create_intent_failed' => 'インテントを作成するときにエラーが発生しました。',
        'error_card' => 'カード番号を入力するときにエラーが発生しました。',
    ],
    'service' => [
        'price_gte' => '金額は無料（¥0）または50円以上になります。',
        'error_create' => 'サービスを作成する時にエラーが発生しました。',
        'create_success' => 'サービスを作成しました。',
        'error_like' => 'サービスにお気に入りのときにエラーが発生しました。',
        'not_found' => 'サービスIDが見つかりません。',
        'error_comment' => 'サービスにコメントするときにエラーが発生しました。',
        'category_id_required' => 'カテゴリーは必須です。',
        'region_id_required' => '地域は必須です。',
        'image_required' => '画像は必須です。',
        'image_max' => '画像は5120kBを超えることはできません。',
        'image_invalid' => '画像ファイル形式はjpeg、png、jpg、gif、svgになります。',
        'service_title_required' => 'タイトルは必須です。',
        'service_title_max' => 'タイトルは最大32文字まで入力してください。',
        'service_detail_required' => 'サービスの詳細は必須です。',
        'service_detail_max' => 'サービスの詳細は最大4000文字で入力してください。',
        'price_required' => 'サービスの金額は必須です。',
        'time_required' => '時間は必須です。',
        'time_required_numeric' => '時間は数字で入力してください。',
        'service_id_array' => 'サービスIDは配列である必要があります。',
        'price_max' => 'サービスの価格が大きすぎますので、処理できません。',
        'service_id_array_not_duplicate' => 'サービスIDの配列は同じIDに存在します。',
        'service_not_exists' => 'サービスが存在しません。',
        'blocked_or_not_exists' => 'サービスがブロックされている又存在しません',
        'service_is_active' => 'サービスの状況が公開されています',
        'service_removed' => 'サービスが削除されました',
        'service_removed_by_user_owner' => 'サービスが所有者による削除されました',
    ],
    'review' => [
        'review_success' => '評価しました。',
        'review_failed' => '評価するときにエラーが発生しました。',
        'cancel_success' => 'キャンセルしました。',
        'cancel_failed' => 'キャンセルするときにエラーが発生しました。',
    ],
    'store' => [
        'id_required' => 'ストアは必須です。',
        'not_found' => 'ストアIDが見つかりません。',
        'error_like' => 'ストアにお気に入りのときにエラーが発生しました。',
        'store_blocked' => 'ストアがブロックされました。',
        'unauthorized_like' => '自分で自分をお気に入りできる。',
        'store_not_exists' => 'ストアIDが存在しません。',
        'latitude_required' => 'The latitude required',
        'longitude_required' => 'The longitude required',
        'upload_file_s3_error' => 'S3へファイルのアップロードはエラーがあります。',
        'get_image_map_google_cloud_has_error' => 'グーグルクラウドから画像マップを取得するエラーがあります',
    ],
    'identity_card' => [
        'image_required' => '画像は必須です。',
        'image_max' => '画像は5120kBを超えることはできません。',
        'image_invalid' => '画像ファイル形式はjpeg、png、jpg、gif、svgになります。',
        'identity_card_not_exits' => '身分証明カードのデータが存在しません。',
        'identity_card_not_process' => '身分証明カードの状況を変更しました。',
    ],
    'request-consulting' => [
        'canceled_request' => '依頼相談はすでにキャンセルされたか、存在しません。',
        'consulting_id_required' => '依頼相談IDは必須です。',
        'consulting_not_exists' => '依頼相談が存在しません。',
        'service_exists_in_progress' => 'サービスにコンサルティング中です',
        'reason_required' => '理由が必要です',
        'cannot_cancel_request' => 'サービスの支払いが完了しました。',
    ],
    'contact' => [
        'contents_required' => '内容は必須です。',
        'answer_required' => '答えが必要です。',
        'email_not_exists' => 'メールが存在しません。',
    ],
    'verify_phone' => [
        'code_required' => 'コードは必須です。',
        'sent_success' => '認証コードを送信しました。電話番号を確認してください。',
        'code_invalid' => '認証コードが無効です。',
        'code_expired' => '認証コードの有効期限が切れてしまいました。',
        'verify_success' => '認証できました。',
        'verify_failed' => 'サーバーのエラーが発生しました。',
        'exist_phone_verified' => 'この電話番号がすでに使用されましたので、別の電話番号を使用してください。',
        'reset_phone_success' => '電話番号を正常にリセットしました。',
        'is_not_phone_verify' => 'あなたの電話番号は確認されていません。',
    ],
    'bonus' => [
        'referrent_success' => 'ボーナスを更新しました。',
        'indecate_success' => 'ボーナスを支給しました。',
        'check_all_boolean' => 'すべての項目はBoolean形である必要があります。',
    ],
    'leave_group' => [
        'leave_success' => 'グループを退会しました。',
        'reason_required' => '退会の理由を入力してください。',
    ],
    'bank_account' => [
        'create_success' => '銀行アカウントを作成しました。',
        'update_success' => '銀行アカウントを更新しました。',
        'category_id_required' => 'カテゴリーは必須です。',
        'category_not_exists' => 'カテゴリーが存在しません。',
        'account_number_required' => 'アカウント番号は必須です。',
        'account_number_max' => 'アカウント番号は最大50文字で入力してください。',
        'account_owner_required' => '自分のアカウントは必須です。',
        'account_owner_max' => 'アカウント番号は最大255文字で入力してください。',
        'bank_name_required' => 'アカウント番号は必須です。',
        'bank_name_max' => 'アカウント番号は最大255文字で入力してください。',
        'branch_name_required' => '支店名は必須です。',
        'branch_name_max' => '支店名は最大255文字で入力してください。',
        'account_not_exists' => 'お支払い先の口座が登録されていないため、申請ができません。',
    ],
    'coupon' => [
        'invalid_code' => 'クーポンコードが無効です。',
        'valid_code' => 'クーポンコードの使用が有効です。',
        'coupon_required' => 'クーポンコードは必須です。',
        'coupon_discount_required' => '割引クーポンは必須です。',
        'coupon_discount_numeric' => '割引クーポンは数字で入力してください。',
        'coupon_discount_between' => '割引クーポンは0から1までで入力してください。',
        'start_date_required' => '開始日は必須です。',
        'start_date_date' => '開始日の形式は無効です。',
        'expire_date_required' => '締切日は必須です。',
        'expire_date_date' => '締切日の形式は無効です。',
        'expire_date_after' => '締切日は開始日より後ろの日付を選択してください。',
    ],
    'store_image' => [
        'create_success' => 'ストア画像がすでに作成されています。',
        'update_success' => 'ストア画像がすでに更新されています。',
        'delete_success' => 'ストア画像がすでに削除されています。',
        'id_required' => 'ストア画像IDは必須です。',
        'id_exists' => 'ストア画像IDが存在しません。',
        'caption_required' => '説明は必須です。',
        'caption_max' => '説明は最大255文字で入力してください。',
    ],
    'store_article' => [
        'create_success' => 'ストア記事がすでに作成されています。',
        'update_success' => 'ストア記事がすでに更新されています。',
        'delete_success' => 'ストア記事がすでに削除されています。',
        'title_required' => 'タイトルは必須です。',
        'title_max' => 'タイトルは最大255文字で入力してください。',
        'contents_required' => '内容は必須です。',
        'file_name_required' => '画像を選択してください。',
        'file_name_image' => '有効な画像ファイル形式を選択してください。',
        'file_name_mimes' => '画像ファイル形式はjpeg、png、jpg、gif、svgになります。',
        'file_name_max' => '画像は5120kBを超えることはできません。',
    ],
    'store_intro' => [
        'create_success' => 'ストア内容がすでに作成されました。',
        'update_success' => 'ストア内容がすでに更新されました。',
        'delete_success' => 'ストア内容がすでに削除されています。',
        'title_required' => 'タイトルは必須です。',
        'title_max' => 'タイトルは最大255文字で入力してください。',
        'contents_required' => '内容は必須です。',
        'file_name_required' => '画像を選択してください。',
        'file_name_image' => '有効な画像ファイル形式を選択してください。',
        'file_name_mimes' => '画像ファイル形式はjpeg、png、jpg、gif、svgになります。',
        'file_name_max' => '画像は5120kBを超えることはできません。',
    ],
    'comment' => [
        'message_required' => 'メッセージを入力してください。',
        'message_max' => 'メッセージはさだい1000文字で入力してください。',
    ],
    'withdraw' => [
        'amount_required' => '金額は必須です。',
        'amount_numeric' => '金額は数値で入力してください。',
        'amount_min' => '金額は50円以上で入力してください。',
        'status_required' => 'ステータスは必須です。',
        'status_in' => '選択されたステータスが無効です。',
        'reason_rejected_required_if' => '拒否理由はあ必須です。',
        'update_status_success' => '出金申請を更新しました。',
        'create_room_chat_error' => 'Firebaseでルームチャットを作成中にエラーが発生しました。',
        'data_not_same' => 'Firebaseのデータがデータベースのデータと一致しません。',
    ],
    'search' => [
        'text_required' => 'テキストは必須です。',
    ],
    'service_review' => [
        'value_required' => '値は必須です。',
        'value_numeric' => '値は数字で入力してください。',
        'message_required' => 'メッセージを入力してください。',
    ],
    'advertising' => [
        'link_path_required' => 'パスリンクは必須です。',
        'block_id_required' => 'ブロックIDは必須です。',
        'block_id_not_exists' => 'ブロックIDが存在しません。',
        'media_id_required' => '広告IDが必須です。',
        'media_id_array' => '広告IDは配列である必要があります。',
        'media_id_not_exists' => '広告IDが存在しません。',
    ],
    'region' => [
        'id_not_exists' => '地域IDが存在しません。',
    ],
    'refferal_bonus' => [
        'data_not_exists' => '紹介ボーナスのデータが存在しません。',
    ],
    'category' => [
        'parent_id_required' => '親IDが必須です。',
        'parent_id_number' => '親IDは整数型である必要があります',
        'parent_id_not_exists' => '親IDが存在しません。',
        'name_required' => 'カテゴリ名が必須です。',
        'name_max' => 'カテゴリ名は255文字以内で入力してください。',
        'prarent_id_not_same_id' => '親IDとIDは同じデータが入力できません。',
        'has_child_data' => 'カテゴリには子データがあります。',
        'has_services' => 'サービスのこのカテゴリーIDは選択されていますので、削除できません。',
    ],

    'exception' => 'システムでエラーが発生しました。',
    'services_not_of_user' => 'このサービスはユーザに属していません。',
    'services_not_exists' => 'サービスが存在しません。',
    'code_invalid' => 'コードが無効です。',
    'user_not_yet_have_point' => 'ユーザーはまだポイントを持っていません。',
    'user_not_enough_point_payment' => 'ユーザーのポイント支払いが不足していません。',
    'request_consulting_not_exists' => '依頼相談が存在しません。',
    'data_not_exists' => 'データが存在しません。',
    'bank_account_exists' => '銀行アカウントが存在しました。',
    'invalid_image' => '画像が無効です。',
];