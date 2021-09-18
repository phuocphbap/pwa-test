<?php

namespace App\Repositories;

use App\Constant\StatusConstant;
use App\Entities\Chat;
use App\Validators\ChatValidator;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Eloquent\BaseRepository;

/**
 * Class ChatRepositoryEloquent.
 */
class ChatRepositoryEloquent extends BaseRepository implements ChatRepository
{
    /**
     * Specify Model class name.
     *
     * @return string
     */
    public function model()
    {
        return Chat::class;
    }

    /**
     * Specify Validator class name.
     *
     * @return mixed
     */
    public function validator()
    {
        return ChatValidator::class;
    }

    /**
     * Boot up the repository, pushing criteria.
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

    public function getListChat($user)
    {
        return $this->model::where('owner_id', $user->id)
                        ->orWhere('customer_id', $user->id)
                        ->with('userCustomer:id,user_name,email,first_name,last_name,avatar,gender')
                        ->with('userOnwer:id,user_name,email,first_name,last_name,avatar,gender')
                        ->with('service:id,service_title');
    }

    /**
     * get room chat by 2 user.
     */
    public function getRoomChat($userId, $customerId, $consultingId)
    {
        return $this->model->where(function ($q1) use ($userId) {
            $q1->where('owner_id', $userId)
                            ->orWhere('customer_id', $userId);
        })
                        ->where(function ($q2) use ($customerId) {
                            $q2->where('owner_id', $customerId)
                            ->orWhere('customer_id', $customerId);
                        })
                        ->where('consulting_id', $consultingId)
                        ->with('userCustomer:id,user_name,first_name,last_name,avatar,gender')
                        ->with('userOnwer:id,user_name,first_name,last_name,avatar,gender')
                        ->with('service:id,service_title')
                        ->first();
    }

    public function getRoomChatByKeyFirebase($keyFirebase)
    {
        return $this->model->where('key_firebase', $keyFirebase)
                        ->with('userCustomer:id,user_name,first_name,last_name,avatar,gender')
                        ->with('userOnwer:id,user_name,first_name,last_name,avatar,gender')
                        ->with('service:id,service_title')
                        ->first();
    }

    /**
     * get all room by user id.
     */
    public function getRoomsByUserId($userId)
    {
        return $this->model->where('owner_id', $userId)
                        ->orWhere('customer_id', $userId)
                        ->get();
    }

    /**
     * update status user when leave group.
     */
    public function updateLeaveGroup($chat, $status)
    {
        $this->model->where('id', $chat->id)
                ->update([
                    'is_leave' => $status,
                ]);

        return true;
    }

    /**
     * update status user when leave group.
     */
    public function updateBlackListAccount($chat, $status)
    {
        $this->model->where('id', $chat->id)
                ->update([
                    'is_black_list' => $status,
                ]);

        return true;
    }

    /**
     * get room chat by consulting_id.
     */
    public function getRoomChatByConsulting($consultingId)
    {
        return $this->model->where('consulting_id', $consultingId)
                        ->where('type', StatusConstant::CHAT_TYPE_CONSULTING)
                        ->with('userCustomer:id,user_name,first_name,last_name,avatar,gender')
                        ->with('userOnwer:id,user_name,first_name,last_name,avatar,gender')
                        ->with('service:id,service_title')
                        ->first();
    }

    /**
     * get room by array key_firebase.
     */
    public function getConsultingByKeyFirebase(array $keyFirebases)
    {
        return $this->model->whereIn('key_firebase', $keyFirebases)->get(['consulting_id', 'key_firebase']);
    }

    /**
     * get room chat by consulting_id.
     */
    public function getListChatExceptRequestConsulting($userId, $search)
    {
        $query = $this->model
                        ->where(function ($query) use ($userId) {
                            $query->where('customer_id', $userId);
                            $query->orWhere('owner_id', $userId);
                        })
                        ->where('type', StatusConstant::CHAT_TYPE_CHATTING)
                        ->with('userCustomer:id,user_name,first_name,last_name,avatar,gender')
                        ->with('userOnwer:id,user_name,first_name,last_name,avatar,gender')
                        ->with('service:id,service_title');

        $data = $query->where(function ($query) use ($search) {
            $query->when($search, function ($query, $search) {
                $query->whereIn('owner_id', function ($query) use ($search) {
                    $query->selectRaw('id from users');
                    $query->where('user_name', 'LIKE', "%{$search}%");
                });
                $query->orWhereIn('customer_id', function ($query) use ($search) {
                    $query->selectRaw('id from users');
                    $query->where('user_name', 'LIKE', "%{$search}%");
                });
            });
        })->orderBy('created_at', 'desc')->get();

        return $data;
    }
}
