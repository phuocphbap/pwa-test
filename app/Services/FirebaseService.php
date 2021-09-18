<?php

namespace App\Services;

use App\Constant\StatusConstant;
use App\Repositories\ChatRepository;
use App\Repositories\RequestConsultingRepository;
use App\Repositories\UserRepository;
use Carbon\Carbon;
use Illuminate\Http\Exceptions\HttpResponseException;
use Kreait\Firebase\Factory;

class FirebaseService
{
    protected $userRepo;
    protected $chatRepo;
    protected $consultingRepo;

    /**
     * FirebaseService constructor.
     */
    public function __construct(
        UserRepository $userRepo,
        ChatRepository $chatRepo,
        RequestConsultingRepository $consultingRepo
    ) {
        $this->firebase = (new Factory())->withServiceAccount(env('FIREBASE_CREDENTIALS'))
                                    ->withDatabaseUri(env('FIREBASE_DATABASE_URI'));
        $this->database = $this->firebase->createDatabase();
        $this->userRepo = $userRepo;
        $this->chatRepo = $chatRepo;
        $this->consultingRepo = $consultingRepo;
    }

    /**
     * handle send text to chatbot.
     */
    public function handleSendTextToChatbot($step, $keyFirebase, $chatbots, $messages, $user, $roomsFirebase, $data, $lastMessage)
    {
        $text = $data['nameOwner'].'さんが「'.$data['nameServices'].'」を引き受けました。金額をご確認のうえ、支払い手続きをしてください。金額は'.$data['price'].'円です。';
        $text1 = '【シェアシテ事務局からの連絡】\n提供者様側でこの依頼を「引き受ける」ボタンを押しましたので、次は依頼者様側で決済手続きを行って下さい。';
        $text2 = 'おめでとうございます！「'.$data['nameServices'].'」の購入が成立しました！実施日に向けて、メッセージのやり取りをしましょう。完了後は、お互いに評価することを忘れないようにしましょう！';
        $text3 = $data['nameCustomer'].'さんが'.$data['nameOwner'].'さんを評価しました。24時間以内に評価をしましょう。お互いを評価後、正式に完了となります。評価もお互いのプロフィールに公開されます。';
        $text4 = $data['nameOwner'].'さんが'.$data['nameCustomer'].'さんを評価しました。24時間以内に評価をしましょう。お互いを評価後、正式に完了となります。評価もお互いのプロフィールに公開されます。';
        switch ($step) {
            case 0:
                $this->createChatBotFirebase($step, $data['consultingId'], $data['serviceId'], $text, $chatbots, $keyFirebase);
                $dataMess = [
                    'sender' => (int) $user->id,
                    'text' => $data['context'],
                    'timestamp' => Carbon::now()->getPreciseTimestamp(3),
                ];
                $messages->getChild($keyFirebase)->push($dataMess);

                $this->createLastMessage($lastMessage, $keyFirebase, $data['context'], $data['consultingId']);
                break;
            case 1:
                $this->createChatBotFirebase($step, $data['consultingId'], $data['serviceId'], $text1, $chatbots, $keyFirebase);
                $this->createLastMessage($lastMessage, $keyFirebase, $text1, $data['consultingId']);
                break;
            case 2:
                $this->createChatBotFirebase($step, $data['consultingId'], $data['serviceId'], $text2, $chatbots, $keyFirebase);
                $this->createLastMessage($lastMessage, $keyFirebase, $text2, $data['consultingId']);
                break;
            case 3:
                $this->createChatBotFirebase($step, $data['consultingId'], $data['serviceId'], $text3, $chatbots, $keyFirebase);
                $this->createLastMessage($lastMessage, $keyFirebase, $text3, $data['consultingId']);
                break;
            case 4:
                $this->createChatBotFirebase($step, $data['consultingId'], $data['serviceId'], $text4, $chatbots, $keyFirebase);
                $this->createLastMessage($lastMessage, $keyFirebase, $text4, $data['consultingId']);

                // Finish progress then update is_completed
                $this->updateRoomAfterFinishProgress($keyFirebase, $roomsFirebase);
                break;
            default:
                // code...
                break;
        }
    }

    /**
     * create push notifications.
     */
    public function pushNotifications($step, $notices, $serviceId, $consultingId, $nameServices)
    {
        switch ($step) {
            case 0:
                $text = '「'.$nameServices.'」の進捗状況が更新されました。\n【依頼相談受取】';
                $this->pushNoticesConsulting($step, $notices, $serviceId, $consultingId, $text);
                break;
            case 1:
                $text = '「'.$nameServices.'」の進捗状況が更新されました。\n【依頼相談送信】→【依頼相談受付】';
                $this->pushNoticesConsulting($step, $notices, $serviceId, $consultingId, $text);
                break;
            case 2:
                $text = '「'.$nameServices.'」の進捗状況が更新されました。\n【依頼相談】→【本契約】';
                $this->pushNoticesConsulting($step, $notices, $serviceId, $consultingId, $text);
                break;
            case 3:
                $text = '「'.$nameServices.'」の進捗状況が更新されました。\n【本契約】→【評価待ち】';
                $this->pushNoticesConsulting($step, $notices, $serviceId, $consultingId, $text);
                break;
            case 4:
                $text = '「'.$nameServices.'」の進捗状況が更新されました。\n【評価待ち】→【完了】';
                $this->pushNoticesConsulting($step, $notices, $serviceId, $consultingId, $text);
                break;
            default:
                break;
        }
    }

    /**
     * push notifications for each step.
     */
    public function pushNoticesConsulting($step, $notices, $serviceId, $consultingId, $text)
    {
        try {
            $consulting = $this->consultingRepo->find($consultingId);
    
            // get switch_notices_progress
            $customer = $this->userRepo->find($consulting->customer_id);
            if ($customer->switch_notices_progress) {
                $dataCustomer = $this->prepareDataProgress($step, $consultingId, $serviceId, $text);
                $notices->getChild('user_id_'.$consulting->customer_id)->getChild('progress')->push($dataCustomer);
            }
    
            $owner = $this->userRepo->find($consulting->owner_id);
            if ($owner->switch_notices_progress) {
                $dataOwner = $this->prepareDataProgress($step, $consultingId, $serviceId, $text);
                $notices->getChild('user_id_'.$consulting->owner_id)->getChild('progress')->push($dataOwner);
            }
    
            return true;
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    /**
     * prepareDataProgress.
     *
     * @param mixed $step
     * @param mixed $consultingId
     * @param mixed $serviceId
     * @param mixed $text
     *
     * @return array
     */
    public function prepareDataProgress($step, $consultingId, $serviceId, $text)
    {
        return [
            'step' => (int) $step,
            'isSeen' => false,
            'isCancel' => false,
            'consulting_id' => (int) $consultingId,
            'service_id' => (int) $serviceId,
            'text' => $text,
            'timestamp' => Carbon::now()->getPreciseTimestamp(3),
            'type' => 'progress',
        ];
    }

    /**
     * get rooom on firebase.
     */
    public function getRoomsFirebase($roomsFirebase, $userId, $ownerId, $consultingId)
    {
        if ($consultingId) {
            $room = array_filter($roomsFirebase, function ($val) use ($userId, $ownerId, $consultingId) {
                return ($val['customer_id'] == $userId || $val['owner_id'] == $userId)
                            && ($val['customer_id'] == $ownerId || $val['owner_id'] == $ownerId)
                            && $val['consulting_id'] == $consultingId;
            });
        } else {
            $room = array_filter($roomsFirebase, function ($val) use ($userId, $ownerId) {
                return ($val['customer_id'] == $userId || $val['owner_id'] == $userId)
                                && ($val['customer_id'] == $ownerId || $val['owner_id'] == $ownerId)
                                && $val['consulting_id'] === 'null';
            });
        }

        return $room;
    }

    public function prepareData($roomName, $userId, $ownerId, $serviceId, $consultingId)
    {
        if ($consultingId) {
            $data = [
                'room_name' => $roomName,
                'customer_id' => (int) $userId,
                'owner_id' => (int) $ownerId,
                'consulting_id' => (int) $consultingId,
                'service_id' => (int) $serviceId,
                'type' => 'consulting',
                'created_at' => Carbon::now()->getPreciseTimestamp(3),
                'is_completed' => false,
                'is_leave' => false,
                'is_black_list' => false,
            ];
        } else {
            $data = [
                'room_name' => $roomName,
                'customer_id' => (int) $userId,
                'owner_id' => (int) $ownerId,
                'consulting_id' => 'null',
                'service_id' => 'null',
                'type' => 'chatting',
                'created_at' => Carbon::now()->getPreciseTimestamp(3),
                'is_completed' => false,
                'is_leave' => false,
                'is_black_list' => false,
            ];
        }

        return $data;
    }

    /**
     * handle block chat when leave group.
     */
    public function handleLeaveChat($userId, $status, $type)
    {
        try {
            $roomsDB = $this->chatRepo->getRoomsByUserId($userId);
            $arrKeyRooms = [];
            foreach ($roomsDB as $key => $value) {
                $this->updateLeaveGroupOrBlackList($type, $value, $status);
                $arrKeyRooms[$key] = $value->key_firebase;
            }
    
            // room on firebase
            $rooms = $this->database->getReference('rooms');
            $roomsFirebase = $rooms->getSnapshot()->getValue();
            $filtered = array_filter(
                $roomsFirebase,
                function ($key) use ($arrKeyRooms) {
                    return in_array($key, array_values($arrKeyRooms));
                },
                ARRAY_FILTER_USE_KEY
            );
    
            // update on fireabse
            $this->handleUpdateChatOnFirebase($rooms, $filtered, $status, $type);
    
            return true;
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    /**
     * update completed when finish progress.
     */
    public function updateRoomAfterFinishProgress($keyFirebase, $roomsFirebase)
    {
        try {
            // update on Firebase
            $data = array_filter($roomsFirebase, function ($key) use ($keyFirebase) {
                return $key == $keyFirebase;
            }, ARRAY_FILTER_USE_KEY);
    
            $rooms = $this->database->getReference('rooms');
            $rooms->getChild($keyFirebase)->set([
                'created_at' => $data[$keyFirebase]['created_at'],
                'consulting_id' => $data[$keyFirebase]['consulting_id'],
                'customer_id' => $data[$keyFirebase]['customer_id'],
                'owner_id' => $data[$keyFirebase]['owner_id'],
                'service_id' => $data[$keyFirebase]['service_id'],
                'type' => $data[$keyFirebase]['type'],
                'is_black_list' => $data[$keyFirebase]['is_black_list'],
                'is_leave' => $data[$keyFirebase]['is_leave'],
                'is_completed' => true,
            ]);
    
            // udpate on Db
            $this->chatRepo->where('key_firebase', $keyFirebase)
                ->update([
                    'is_completed' => true,
                ]);
    
            return true;
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    /**
     * function update account leave group || black list.
     */
    public function updateLeaveGroupOrBlackList($type, $value, $status)
    {
        switch ($type) {
            case StatusConstant::USER_BLACKLIST:
                return $this->chatRepo->updateBlackListAccount($value, $status);
                break;
            case StatusConstant::USER_LEAVE_GROUP:
                return $this->chatRepo->updateLeaveGroup($value, $status);
                break;
            default:
                break;
        }
    }

    /**
     * update status on firebase when leave group | black list.
     */
    public function handleUpdateChatOnFirebase($rooms, $data, $status, $type)
    {
        switch ($type) {
            case StatusConstant::USER_BLACKLIST:
                foreach ($data as $key => $value) {
                    $rooms->getChild($key)->set([
                        'created_at' => $value['created_at'],
                        'consulting_id' => $value['consulting_id'],
                        'customer_id' => $value['customer_id'],
                        'owner_id' => $value['owner_id'],
                        'service_id' => $value['service_id'],
                        'type' => $value['type'],
                        'is_leave' => $value['is_leave'],
                        'is_completed' => $value['is_completed'],
                        'is_black_list' => $status,
                    ]);
                }

                return true;
                break;
            case StatusConstant::USER_LEAVE_GROUP:
                foreach ($data as $key => $value) {
                    $rooms->getChild($key)->set([
                        'created_at' => $value['created_at'],
                        'consulting_id' => $value['consulting_id'],
                        'customer_id' => $value['customer_id'],
                        'owner_id' => $value['owner_id'],
                        'service_id' => $value['service_id'],
                        'type' => $value['type'],
                        'is_black_list' => $value['is_black_list'],
                        'is_completed' => $value['is_completed'],
                        'is_leave' => $status,
                    ]);
                }

                return true;
                break;
            default:
                break;
        }
    }

    /**
     * create chatbot on firebase.
     */
    public function createChatBotFirebase($step, $consultingId, $serviceId, $text, $chatbots, $keyFirebase)
    {
        try {
            $data = [
                'step' => (int) $step,
                'consulting_id' => (int) $consultingId,
                'service_id' => (int) $serviceId,
                'text' => $text,
                'timestamp' => Carbon::now()->getPreciseTimestamp(3),
            ];
            $chatbots->getChild($keyFirebase)->push($data);
    
            return true;
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    /**
     * create last messages on firebase.
     */
    public function createLastMessage($lastMessage, $keyFirebase, $text, $consultingId)
    {
        try {
            $consulting = $this->consultingRepo->find($consultingId);
            $data = [
                'text' => $text,
                'isSeen' => false,
                'timestamp' => Carbon::now()->getPreciseTimestamp(3),
            ];
            $lastMessage->getChild('user_id_'.$consulting->customer_id)->getChild($keyFirebase)->set($data);
            $lastMessage->getChild('user_id_'.$consulting->owner_id)->getChild($keyFirebase)->set($data);
    
            return true;
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    /**
     * push notification verify sms.
     */
    public function pushNoticeVerifySMS($user)
    {
        try {
            $notices = $this->database->getReference('notifications');
            $text = 'SMS認証が完了しました。';
            $data = [
                'text' => $text,
                'isSeen' => false,
                'timestamp' => Carbon::now()->getPreciseTimestamp(3),
                'store_id' => (int) $user->store->id,
                'type' => 'sms',
            ];
            $notices->getChild('user_id_'.$user->id)->getChild('sms')->push($data);
    
            return true;
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    /**
     * push notification request withdraw.
     */
    public function noticeRequestWithDraw($user)
    {
        try {
            $notices = $this->database->getReference('notifications');
            $text = '出金申請の処理を待っています。';
            $data = [
                'text' => $text,
                'isSeen' => false,
                'timestamp' => Carbon::now()->getPreciseTimestamp(3),
                'type' => 'withdraw',
            ];
            $notices->getChild('user_id_'.$user->id)->getChild('withdraw')->push($data);
    
            return true;
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    /**
     * push notification when has comment.
     */
    public function noticeCommentInService($userId, $serviceId, $nameServices)
    {
        try {
            $notices = $this->database->getReference('notifications');
            $text = $nameServices.'サービスに新しいコメントがあります。';
            $data = [
                'text' => $text,
                'isSeen' => false,
                'timestamp' => Carbon::now()->getPreciseTimestamp(3),
                'service_id' => $serviceId,
                'type' => 'comments',
            ];
            $notices->getChild('user_id_'.$userId)->getChild('comments')->push($data);
    
            return true;
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    /**
     * push notification cancel request consulting service.
     */
    public function noticeCancelConsultingService($consultingId)
    {
        try {
            $consulting = $this->consultingRepo->find($consultingId);
    
            // get switch_notices_progress
            $owner = $this->userRepo->find($consulting->customer_id);
            if ($owner->switch_notices_progress) {
                $notices = $this->database->getReference('notifications');
                $text = $consulting->customer->user_name.'さんが【'.$consulting->service->service_title.'】をキャンセルしました。';
                $data = [
                    'step' => null,
                    'isSeen' => false,
                    'isCancel' => true,
                    'consulting_id' => (int) $consultingId,
                    'service_id' => (int) $consulting->service_id,
                    'text' => $text,
                    'timestamp' => Carbon::now()->getPreciseTimestamp(3),
                    'type' => 'progress',
                ];
                $notices->getChild('user_id_'.$consulting->owner_id)->getChild('progress')->push($data);
            }
    
            return true;
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    /**
     * get unread message in progress by user.
     */
    public function getLatestProgressUnreadMessage($user, $progress)
    {
        try {
            $lastMess = $this->database->getReference('last_messages')->getChild('user_id_'.$user->id)
                                    ->getValue();
            $unreadMess = array_filter($lastMess, function ($value) {
                return $value['isSeen'] == false;
            });

            $rooms = $this->chatRepo->getRoomsByUserId($user->id)->where('type', StatusConstant::CHAT_TYPE_CONSULTING)->values();
            $keyRoomOnDB = [];
            foreach ($rooms as $key => $value) {
                $keyRoomOnDB[$key] = $value->key_firebase;
            }

            if (empty($unreadMess) || empty($keyRoomOnDB)) {
                return false;
            }

            $filter = array_filter($unreadMess, function ($key) use ($keyRoomOnDB) {
                return in_array($key, array_values($keyRoomOnDB));
            }, ARRAY_FILTER_USE_KEY);

            // sort latest messages unread
            foreach ($filter as $key => $node) {
                $timestamps[$key] = $node['timestamp'];
            }
            array_multisort($timestamps, SORT_DESC, $filter);

            // assign number for sort
            $keyFilter = array_keys($filter);
            $i = 0;
            foreach ($filter as $key => $value) {
                $filter[$key]['sort'] = $i++;
            }
            $filter = collect($filter);

            $chats = $this->chatRepo->getConsultingByKeyFirebase(array_values($keyFilter));
            $temp = $filter->map(function ($item, $key) use ($chats) {
                $result = $chats->where('key_firebase', $key);

                return collect($item)->merge($result);
            });
            $data = [];
            foreach ($temp as $key => $value) {
                $data[$key]['consulting_id'] = $value[0]['consulting_id'];
                $data[$key]['order'] = $value['sort'];
            }
            $data = collect(array_values($data));

            // compare with progress
            $progress = $progress->whereIn('id', $data->pluck('consulting_id'))->get();
            $progress = $progress->map(function ($item, $key) use ($data) {
                $result = $data->where('consulting_id', $item->id);

                return collect($item)->merge($result);
            });
            $progress = $progress->toArray();
            foreach ($progress as $key => $value) {
                $progress[$key]['order'] = $value[0]['order'];
            }

            return collect($progress)->sortBy('order');
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    /**
     * updateSwitchNoticesProgress.
     *
     * @param int $userId
     * @param int $type
     *
     * @return void
     */
    public function updateSwitchNoticesProgress($userId, $type)
    {
        return $this->userRepo->updateSwitchNoticesProgress($userId, $type);
    }

    /**
     * createRoomChatRequestWithdraw.
     *
     * @param mixed $userId
     *
     * @return object
     */
    public function createRoomChatRequestWithdraw($userId)
    {
        try {
            $roomDB = $this->chatRepo->findWhere(['customer_id' => $userId, 'type' => 'withdraw'])->first();
            $rooms = $this->database->getReference('rooms');
            $roomFB = $rooms->getSnapshot()->getValue();
    
            $roomFB = array_filter($roomFB, function ($value) use ($userId) {
                return $value['customer_id'] == $userId && $value['type'] == 'withdraw';
            });
    
            if (!$roomDB && !$roomFB) {
                $data = [
                    'room_name' => null,
                    'customer_id' => (int) $userId,
                    'owner_id' => 'null',
                    'consulting_id' => 'null',
                    'service_id' => 'null',
                    'type' => 'withdraw',
                    'created_at' => Carbon::now()->getPreciseTimestamp(3),
                    'is_completed' => false,
                    'is_leave' => false,
                    'is_black_list' => false,
                ];
    
                // insert in Firebase
                $room = $rooms->push($data);
                $rooms->orderByKey()->getSnapshot();
                $key = $room->getKey();
    
                // insert in database
                if ($key) {
                    $data['owner_id'] = null;
                    $data['consulting_id'] = null;
                    $data['service_id'] = null;
                    $data['key_firebase'] = $key;
                    $this->chatRepo->create($data);
    
                    return $this->chatRepo->getRoomChatByKeyFirebase($key);
                } else {
                    $response = response()->json(['errors' => true, 'messages' => __('api.withdraw.create_room_chat_error')]);
                    throw new HttpResponseException($response);
                }
            } elseif ($roomDB && $roomFB && $roomDB->key_firebase == array_keys($roomFB)[0]) {
                return $this->chatRepo->getRoomChatByKeyFirebase($roomDB->key_firebase);
            } elseif ($roomDB && $roomFB && $roomDB->key_firebase != array_keys($roomFB)[0]) {
                $response = response()->json(['errors' => true, 'messages' => __('api.withdraw.data_not_same')]);
                throw new HttpResponseException($response);
            } elseif (($roomDB && !$roomFB) || (!$roomDB && $roomFB)) {
                $response = response()->json(['errors' => true, 'messages' => __('api.validation.room_data_has_been_deleted')]);
                throw new HttpResponseException($response);
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    /**
     * createChatbotWithdraw.
     *
     * @param string $keyFirebase
     *
     * @return void
     */
    public function createChatbotWithdraw($keyFirebase, $text)
    {
        try {
            $chatbots = $this->database->getReference('chatbots');
            $data = [
                'step' => 'null',
                'consulting_id' => 'null',
                'service_id' => 'null',
                'text' => $text,
                'timestamp' => Carbon::now()->getPreciseTimestamp(3),
            ];
            $chatbots->getChild($keyFirebase)->push($data);
    
            return true;
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    /**
     * createLastMessageWithdraw.
     *
     * @param int    $userId
     * @param string $keyFirebase
     * @param string $text
     *
     * @return bool
     */
    public function createLastMessageWithdraw($userId, $keyFirebase, $text)
    {
        try {
            $lastMessage = $this->database->getReference('last_messages');
            $data = [
                'text' => $text,
                'isSeen' => false,
                'timestamp' => Carbon::now()->getPreciseTimestamp(3),
            ];
            $lastMessage->getChild('user_id_'.$userId)->getChild($keyFirebase)->set($data);
    
            return true;
        } catch (\Throwable $th) {
            throw $th;
        }
    }
    
    /**
     * pushNoticeProgressPaymentFinish
     *
     * @param  mixed $step
     * @param  mixed $serviceId
     * @param  mixed $consultingId
     * @param  mixed $nameServices
     *
     * @return bool
     */
    public function pushNoticeProgressPaymentFinish($step, $serviceId, $consultingId, $nameServices)
    {
        $notices = $this->database->getReference('notifications');

        return $this->pushNotifications($step, $notices, $serviceId, $consultingId, $nameServices);
    }
    
    /**
     * noticeCancelProgress
     *
     * @param mixed $progress
     *
     * @return bool
     */
    public function noticeCancelProgress($progress, $user)
    {
        try {
            // get switch_notices_progress
            if ($user->switch_notices_progress) {
                $notices = $this->database->getReference('notifications');
                $serviceName = $progress->service->service_title ?? null;
                $text = '「サービス'.$serviceName.'」の進捗状況が更新されました。\n【依頼相談送信】→【依頼相談のお断り】';
                $data = [
                    'step' => null,
                    'isSeen' => false,
                    'isCancel' => true,
                    'consulting_id' => (int) $progress->id,
                    'service_id' => (int) $progress->service_id,
                    'text' => $text,
                    'timestamp' => Carbon::now()->getPreciseTimestamp(3),
                    'type' => 'progress',
                ];
                $notices->getChild('user_id_'.$progress->customer_id)->getChild('progress')->push($data);
            }
    
            return true;
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    
    /**
     * getRoomChatProgress
     *
     * @param mixed $consultingId
     *
     * @return object
     */
    public function getRoomChatProgress($consultingId)
    {
        try {
            $roomDB = $this->chatRepo->findWhere(['consulting_id' => $consultingId, 'type' => 'consulting'])->first();
            $rooms = $this->database->getReference('rooms');
            $roomFB = $rooms->getSnapshot()->getValue();
    
            $roomFB = array_filter($roomFB, function ($value) use ($consultingId) {
                return $value['consulting_id'] == $consultingId && $value['type'] == 'consulting';
            });
    
            if ($roomDB && $roomFB && $roomDB->key_firebase == array_keys($roomFB)[0]) {
                return $this->chatRepo->getRoomChatByKeyFirebase($roomDB->key_firebase);
            } elseif ($roomDB && $roomFB && $roomDB->key_firebase != array_keys($roomFB)[0]) {
                $response = response()->json(['errors' => true, 'messages' => __('api.withdraw.data_not_same')]);
                throw new HttpResponseException($response);
            } elseif (($roomDB && !$roomFB) || (!$roomDB && $roomFB)) {
                $response = response()->json(['errors' => true, 'messages' => __('api.validation.room_data_has_been_deleted')]);
                throw new HttpResponseException($response);
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }
    
    /**
     * createChatbotProgress
     *
     * @param string $keyFirebase
     * @param object $progress
     * @param string $text
     *
     * @return bool
     */
    public function createChatbotProgress(string $keyFirebase, object $progress, string $text)
    {
        try {
            $chatbots = $this->database->getReference('chatbots');
            $data = [
                'step' => 'null',
                'consulting_id' => $progress->id,
                'service_id' => $progress->service_id,
                'text' => $text,
                'timestamp' => Carbon::now()->getPreciseTimestamp(3),
            ];
            $chatbots->getChild($keyFirebase)->push($data);
    
            return true;
        } catch (\Throwable $th) {
            throw $th;
        }
    }
    
    /**
     * createLastMessageProgress
     *
     * @param mixed $keyFirebase
     * @param mixed $progress
     * @param mixed $text
     *
     * @return void
     */
    public function createLastMessageProgress($keyFirebase, $progress, $text)
    {
        try {
            $lastMessage = $this->database->getReference('last_messages');
            $data = [
                'text' => $text,
                'isSeen' => false,
                'timestamp' => Carbon::now()->getPreciseTimestamp(3),
            ];
            $lastMessage->getChild('user_id_'.$progress->customer_id)->getChild($keyFirebase)->set($data);
            $lastMessage->getChild('user_id_'.$progress->owner_id)->getChild($keyFirebase)->set($data);
    
            return true;
        } catch (\Throwable $th) {
            throw $th;
        }
    }
    
    /**
     * handleSendMessageProgress
     *
     * @param object $progress
     * @param string $reason
     *
     * @return void
     */
    public function handleSendMessageProgress(object $progress, string $reason)
    {
        try {
            $text = '出品者からサービス提供のお断りメッセージが届きました。\nお断り理由：'. $reason;
            $room = $this->getRoomChatProgress($progress->id);
            $this->createChatbotProgress($room->key_firebase, $progress, $text);
            $this->createLastMessageProgress($room->key_firebase, $progress, $text);

            return true;
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
