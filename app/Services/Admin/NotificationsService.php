<?php

namespace App\Services\Admin;

use Carbon\Carbon;
use Kreait\Firebase\Factory;
use App\Constant\StatusConstant;
use App\Entities\WithdrawRequest;

class NotificationsService
{

    /**
     * constructor.
     */
    public function __construct()
    {
        $this->firebase = (new Factory)->withServiceAccount(env('FIREBASE_CREDENTIALS'))
                                    ->withDatabaseUri(env('FIREBASE_DATABASE_URI'));
        $this->database = $this->firebase->createDatabase();
        $this->notices = $this->database->getReference('notifications');
    }

    /**
     * create notices for verify identity card
     */
    public function noticeVerifyIdentityCard($type, $userId)
    {
        switch ($type) {
            case StatusConstant::IDENTITY_ACCEPT_STATUS:
                $text = '身分証明書申請が承認されました。';
                $this->createNoticeIDCard($userId, $text, false);
                break;
            case StatusConstant::IDENTITY_REJECT_STATUS:
                $text = '身分証明書が拒否されましたので、情報を再確認してください。';
                $this->createNoticeIDCard($userId, $text, true);
                break;
            default:
                break;
        }
    }

    /**
     * sub create notices create identity card
     */
    public function createNoticeIDCard($userId, $text, $isRejected)
    {
        $data = [
            'isSeen' => false,
            'is_rejected' => $isRejected,
            'text' => $text,
            'timestamp' => Carbon::now()->getPreciseTimestamp(3),
            'type' => 'identity_card',
        ];
        $this->notices->getChild('user_id_'.$userId)->getChild('identity_card')->push($data);

        return true;
    }
    
    /**
     * noticeCancelApprovedIDCard
     *
     * @param int $userId
     *
     * @return bool
     */
    public function noticeCancelApprovedIDCard(int $userId)
    {
        $data = [
            'isSeen' => false,
            'is_rejected' => true,
            'text' => '管理者から身分証明書の承認がキャンセルされました。',
            'timestamp' => Carbon::now()->getPreciseTimestamp(3),
            'type' => 'identity_card',
        ];
        $this->notices->getChild('user_id_'.$userId)->getChild('identity_card')->push($data);

        return true;
    }

    /**
     * create notices for bonus user
     */
    public function noticeBonusUser($userId, $point, $dateExpire)
    {
        $text = 'あなたは管理者からの'. $point .'ポイントを受け取り、有効期限は '. $dateExpire .'までです。';
        $data = [
            'isSeen' => false,
            'text' => $text,
            'timestamp' => Carbon::now()->getPreciseTimestamp(3),
            'type' => 'bonuses',
        ];
        $this->notices->getChild('user_id_'.$userId)->getChild('bonuses')->push($data);
    }

    /**
     * push notification withdraw point
     */
    public function noticeWithDrawPoint($userId, $type, $reason = null)
    {
        switch ($type) {
            case WithdrawRequest::ACCEPTED_STATE:
                $text = '出金申請が処理されております。';
                $this->handleNoticeWithDraw($userId, $text);
                break;
            case WithdrawRequest::DONE_STATE:
                $text = '出金申請が承認されました。';
                $this->handleNoticeWithDraw($userId, $text);
                break;
            case WithdrawRequest::REJECTED_STATE:
                $text = '出金申請が拒否されてしまいました。理由：'. $reason;
                $this->handleNoticeWithDraw($userId, $text);
                break;
            default:
                break;
        }
    }

    /**
     * sub handle create notice
     */
    public function handleNoticeWithDraw($userId, $text)
    {
        $data = [
            'isSeen' => false,
            'text' => $text,
            'timestamp' => Carbon::now()->getPreciseTimestamp(3),
            'type' => 'withdraw',
        ];
        $this->notices->getChild('user_id_'.$userId)->getChild('withdraw')->push($data);
    }

    /**
     * push notices for contact
     */
    public function noticesContact($user, $question)
    {
        $text = 'あなたの質問が回答されました。メールのINBOXから回答内容をご確認ください。\n「'. $question .'」';
        $data = [
            'isSeen' => false,
            'text' => $text,
            'timestamp' => Carbon::now()->getPreciseTimestamp(3),
            'type' => 'contacts',
        ];
        $this->notices->getChild('user_id_'.$user->id)->getChild('contacts')->push($data);
    }

    /**
     * bonus when input referral code
     */
    public function noticeBonusInputReferralCode($userId, $point)
    {
        $text = 'アカウントに'. $point .'ポイントが加算されました';
        $data = [
            'isSeen' => false,
            'text' => $text,
            'timestamp' => Carbon::now()->getPreciseTimestamp(3),
            'type' => 'bonuses',
        ];
        $this->notices->getChild('user_id_'.$userId)->getChild('bonuses')->push($data);
    }
    
    /**
     * noticeBlockService
     *
     * @param object $service
     * @param string $reason
     *
     * @return void
     */
    public function noticeBlockService(object $service, string $reason)
    {
        $text = '管理者から「'. $service->service_title .'」サーブスが削除されました。\n理由は「'. $reason .'」';
        $data = [
            'isSeen' => false,
            'text' => $text,
            'timestamp' => Carbon::now()->getPreciseTimestamp(3),
            'type' => 'services',
        ];
        $this->notices->getChild('user_id_'.$service->user_id)->getChild('services')->push($data);
    }

    /**
     * create notices for reset phone verify
     */
    public function noticeResetPhone($userId)
    {
        $text = '電話番号がリセットされました。電話番号を更新してください';
        $data = [
            'isSeen' => false,
            'text' => $text,
            'timestamp' => Carbon::now()->getPreciseTimestamp(3),
            'type' => 'resetphone',
        ];
        $this->notices->getChild('user_id_'.$userId)->getChild('resetphone')->push($data);
    }

}
