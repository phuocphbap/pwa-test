<?php

namespace App\Http\Controllers\Api\Firebase;

use Carbon\Carbon;
use Kreait\Firebase;
use Illuminate\Http\Request;
use Kreait\Firebase\Factory;
use App\Services\FirebaseService;
use App\Http\Requests\ChatRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Repositories\ChatRepository;
use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\SendTextToChatbotRequest;
use App\Http\Requests\SwitchNoticesProgressRequest;

class FirebaseController extends ApiController
{

    protected $firebase;
    protected $database;
    protected $rooms;
    protected $snapshot;
    protected $chat;
    protected $firebaseService;

    public function __construct(ChatRepository $chat, FirebaseService $firebaseService)
    {
        $this->firebase = (new Factory)->withServiceAccount(env('FIREBASE_CREDENTIALS'))
                                    ->withDatabaseUri(env('FIREBASE_DATABASE_URI'));
        $this->database = $this->firebase->createDatabase();
        $this->rooms = $this->database->getReference('rooms');
        $this->snapshot = $this->rooms->getSnapshot();

        $this->chat = $chat;
        $this->firebaseService = $firebaseService;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ChatRequest $request)
    {
        try {
            \DB::beginTransaction();
            $userId = auth()->user()->id;
            $roomName = $request->room_name ?? null;
            $ownerId = $request->owner_id ?? null;
            $serviceId = $request->service_id ?? null;
            $consultingId = $request->consulting_id ?? null;
            if ($userId == $ownerId) {
                return $this->respondError(__('api.validation.user_owner_invalid'));
            }

            $rooms = $this->chat->getRoomChat($userId, $ownerId, $consultingId);
            $roomsFirebase = $this->snapshot->getValue();

            $getRoomFirebase = [];
            if ($roomsFirebase) {
                $getRoomFirebase = $this->firebaseService->getRoomsFirebase($roomsFirebase, $userId, $ownerId, $consultingId);
            }

            if (!$rooms && !$getRoomFirebase) {
                $data = $this->firebaseService->prepareData($roomName, $userId, $ownerId, $serviceId, $consultingId);
                $room = $this->rooms->push($data);
                $this->rooms->orderByKey()->getSnapshot();
                $key = $room->getKey();
                if ($key) {
                    // store to db
                    if (!$consultingId) {
                        $data['service_id'] = null;
                        $data['consulting_id'] = null;
                    }
                    $data['key_firebase'] = $key;
                    $this->chat->create($data);
                    $rooms = $this->chat->getRoomChatByKeyFirebase($key);
                    \DB::commit();

                    return $this->respondSuccess($rooms);
                }
            }

            if (($rooms && !$getRoomFirebase) || (!$rooms && $getRoomFirebase) || ($rooms && $getRoomFirebase && $rooms->key_firebase != key($getRoomFirebase))) {
                return $this->respondError(__('api.validation.room_data_has_been_deleted'));
            } else {
                return $this->respondSuccess($rooms);
            }
        } catch (\Throwable $th) {
            \Log::ERROR('Controllers\Api\Firebase\FirebaseController - store : '.$th->getMessage());
            \DB::rollback();
            return $this->respondError(__('api.exception'));
        }
    }

    /**
     * send text to chatbot
     */
    public function sendTextToChatbots(SendTextToChatbotRequest $request)
    {
        try {
            DB::beginTransaction();
            $price = $request->price ?? null;
            $nameCustomer = $request->name_customer ?? null;
            $nameOwner = $request->name_owner ?? null;
            $nameServices = $request->name_services ?? null;
            $context = $request->text ?? null;
            $user = auth()->user();

            $chatbots = $this->database->getReference('chatbots');
            $messages = $this->database->getReference('messages');
            $notices = $this->database->getReference('notifications');
            $lastMessage = $this->database->getReference('last_messages');
            $rooms = $this->snapshot->getValue();
            $checkRoomKeyFirebase = array_key_exists($request->room_key, $rooms);
            $roomOnDB = $this->chat->findWhere(['key_firebase' => $request->room_key])->first();

            if (($checkRoomKeyFirebase && !$roomOnDB) || (!$checkRoomKeyFirebase && $roomOnDB)) {
                return $this->respondError(__('api.validation.room_data_has_been_deleted'));
            }

            $dataChatbot = [
                'serviceId' => $request->service_id,
                'nameCustomer' => $nameCustomer,
                'nameOwner' => $nameOwner,
                'nameServices' => $nameServices,
                'price' => $price,
                'context' => $context,
                'consultingId' => $request->consulting_id,
            ];
            if ($checkRoomKeyFirebase && $roomOnDB) {
                $this->firebaseService->handleSendTextToChatbot($request->step, $roomOnDB->key_firebase, $chatbots, $messages, $user, $rooms, $dataChatbot, $lastMessage);
                $this->firebaseService->pushNotifications($request->step, $notices, $request->service_id, $request->consulting_id, $nameServices);
                DB::commit();
                return $this->respondSuccess(null);
            } else {
                return $this->respondError(__('api.data_not_exists'));
            }
        } catch (\Throwable $th) {
            Log::ERROR('Controllers\Api\Firebase\FirebaseController - sendTextToChatbots : '.$th->getMessage());
            DB::rollback();
            return $this->respondError(__('api.exception'));
        }
    }

    /**
     * switchNoticesProgress
     *
     * @param  SwitchNoticesProgressRequest $request
     * @return void
     */
    public function switchNoticesProgress(SwitchNoticesProgressRequest $request)
    {
        try {
            $user = auth()->user();
            $this->firebaseService->updateSwitchNoticesProgress($user->id, $request->type);

            return $this->respondSuccess(null);
        } catch (\Throwable $th) {
            Log::error('Controllers\Api\Firebase\FirebaseController - switchNoticesProgress : '.$th->getMessage());
            return $this->respondError(__('api.exception'));
        }
    }
}
