<?php

namespace App\Repositories;

use App\Entities\IdentityCard;
use App\Entities\SocialAccount;
use App\Entities\Store;
use App\Entities\User;
use App\Entities\UserCoupon;
use App\Entities\VStore;
use App\Entities\Wallet;
use App\Helpers\Facades\UploadS3Helper;
use App\Mail\VerifyRegisterUser;
use App\Support\Social;
use App\Validators\UserValidator;
use Carbon\Carbon;
use DB;
use Mail;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Eloquent\BaseRepository;

/**
 * Class UserRepositoryEloquent.
 */
class UserRepositoryEloquent extends BaseRepository implements UserRepository
{
    /**
     * Specify Model class name.
     *
     * @return string
     */
    public function model()
    {
        return User::class;
    }

    /**
     * Specify Validator class name.
     *
     * @return mixed
     */
    public function validator()
    {
        return UserValidator::class;
    }

    /**
     * Boot up the repository, pushing criteria.
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

    /**
     * generate unique referral code for user.
     */
    public function generateReferralCode()
    {
        //convert time to hexadecimal
        $nowTime = \Carbon\Carbon::now()->timestamp;

        return strtoupper(dechex($nowTime));
    }

    /**
     * register account.
     */
    public function registerUser($data)
    {
        try {
            DB::beginTransaction();
            $data['referral_code'] = $this->generateReferralCode();
            $dataRespone = $this->model->create($data);
            if ($dataRespone) {
                $storeData['user_id'] = $dataRespone['id'];
                Store::create($storeData);
            }
            DB::commit();

            return $dataRespone;
        } catch (\Exception $e) {
            DB::rollBack();

            throw new \Exception($e->getMessage());
        }
    }

    /**
     * get user by social id.
     */
    public function getUserBySocialId($provider, $socialId)
    {
        $userSocial = SocialAccount::where('social_id', $socialId)->where('provider', $provider)->first();
        if ($userSocial) {
            $user = $this->model->find($userSocial->user_id);

            return $user;
        }

        return null;
    }

    /**
     * get user by email.
     */
    public function getUserByEmail($email)
    {
        return $this->model->where('email', $email)->first();
    }

    public function createSocial($data)
    {
        try {
            $dataRespone = SocialAccount::create($data);

            return $dataRespone;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * get point valid of user.
     */
    public function getPointAndExpirePointOfUser($userId)
    {
        $wallet = Wallet::where('user_id', $userId)->first();
        $walletExpire = $wallet->walletExpires->where('expire_date', '>=', \Carbon\Carbon::now())->sortByDesc('id')->first();
        $amountExpire = isset($walletExpire) ? $walletExpire->amount : 0;

        return $wallet->amount + $amountExpire;
    }

    /**
     * get point valid of user.
     */
    public function getPointOfUser($userId)
    {
        $wallet = Wallet::where('user_id', $userId)->first();
        return $wallet->amount;
    }

    /**
     * get point expire in 3 months.
     */
    public function getPointExpireInThreeMonths($userId)
    {
        $wallet = Wallet::where('user_id', $userId)->first();
        $walletExpire = $wallet->walletExpires->where('expire_date', '>=', \Carbon\Carbon::now())
                            ->where('expire_date', '<=', \Carbon\Carbon::now()->addMonth(3))
                            ->sortByDesc('id')
                            ->first();
        $amountExpire = isset($walletExpire) ? $walletExpire->amount : 0;

        return $amountExpire;
    }

    /**
     * get wallet expire in 3 months.
     */
    public function getWalletExpireInThreeMonths($userId)
    {
        $wallet = Wallet::where('user_id', $userId)->first();
        $walletExpire = $wallet->walletExpires->where('expire_date', '>=', \Carbon\Carbon::now())
                            ->where('expire_date', '<=', \Carbon\Carbon::now()->addMonth(3))
                            ->sortByDesc('id')
                            ->first();

        return $walletExpire;
    }

    /**
     * get point expire.
     */
    public function getPointValidWalletExpire($userId)
    {
        $wallet = Wallet::where('user_id', $userId)->first();
        $walletExpire = $wallet->walletExpires->where('expire_date', '>=', \Carbon\Carbon::now())->sortByDesc('id')
                            ->first();

        $amountExpire = isset($walletExpire) ? $walletExpire->amount : 0;

        return $amountExpire;
    }

    /**
     * get wallet expire.
     */
    public function getWalletExpire($userId)
    {
        $wallet = Wallet::where('user_id', $userId)->first();
        $walletExpire = $wallet->walletExpires->where('expire_date', '>=', \Carbon\Carbon::now())->sortByDesc('id')
                            ->first();

        return $walletExpire;
    }

    /**
     * get wallet.
     */
    public function getWallet($userId)
    {
        $wallet = Wallet::where('user_id', $userId)->first();

        return $wallet;
    }

    /**
     * check user already used coupon or not.
     */
    public function checkAlreadyUseCoupon($userId, $couponId)
    {
        $data = UserCoupon::where('user_id', $userId)->where('coupon_id', $couponId)->first();
        if ($data) {
            return true;
        }

        return false;
    }

    /**
     * get detail of user.
     */
    public function getDetailUser($id)
    {
        return VStore::with('categories')->where('user_id', $id)->first();
    }

    /**
     * update profile user.
     */
    public function updateProfile($data, $id)
    {
        //check user update info from kyc screen
        if (isset($data['is_kyc_profiled']) && $data['is_kyc_profiled'] != 0) {
            $data['is_kyc_profiled'] = User::KYC_PROFILE_UPDATED;
        }

        $success = $this->model->findOrFail($id)->update($data);
        if (isset($data['email']) && $success) {
            $this->sendMailConfirm($data['email'], $data['user_name']);
        }

        return true;
    }

    /**
     * update images identity card of user.
     */
    public function updateIdentityCard($userId, $images)
    {
        try {
            DB::beginTransaction();
            $imageArray = [];
            foreach ($images as $image) {
                $imageLink = UploadS3Helper::uploadImage($image, 'identity_card');
                array_push($imageArray, $imageLink);
            }

            $data = IdentityCard::create([
                'user_id' => $userId,
                'images' => $imageArray,
            ]);
            if ($data) {
                $this->updateIdentityStatus($userId, User::IDENTITY_PROCESS_STATUS);
            }
            DB::commit();

            return $data;
        } catch (\Exception $e) {
            DB::rollBack();

            return false;
        }
    }

    /**
     * update images identity card of user.
     */
    public function cancelAprovedIdentityCard($userId)
    {
        try {
            DB::beginTransaction();
            $identity = IdentityCard::where('user_id', $userId)->first();
            if ($identity) {
                foreach ($identity->images as $image) {
                    UploadS3Helper::deleteImage($image);
                }
                IdentityCard::where('user_id', $userId)->delete();
            }
            $this->updateIdentityStatus($userId, User::IDENTITY_PENDING_STATUS);
            DB::commit();

            return true;
        } catch (\Exception $e) {
            DB::rollBack();

            return false;
        }
    }

    /**
     * update user when leave group.
     */
    public function updateLeaveGroup($userId, $reason)
    {
        $this->model->where('id', $userId)
                ->update([
                    'state' => User::USER_LEAVE_GROUP,
                    'reason_leave' => $reason,
                ]);

        return true;
    }

    /**
     * update user verify identity card.
     */
    public function updateIdentityStatus($userId, $status)
    {
        $this->model->where('id', $userId)
                ->update([
                    'identity_status' => $status,
                ]);

        return true;
    }

    /**
     * unlock leave group.
     */
    public function unlockLeaveGroup($userId)
    {
        $this->model->where('id', $userId)
                ->update([
                    'state' => User::USER_ACTIVE,
                ]);

        return true;
    }

    /**
     * get list user.
     */
    public function getListUser($filter)
    {
        $search = $filter->search;
        $user = $this->model->selectRaw('CONCAT(users.first_name, " ", users.last_name) as full_name, users.*')
            ->selectRaw('CASE
                            WHEN users.state = '.User::USER_BLACKLIST.' THEN true
                            ELSE false END
                         AS is_black_list');

        $response = $this->model->joinSub($user, 'user_temp', function ($query) {
            $query->on('user_temp.id', '=', 'users.id');
        })->select('user_temp.*')
            ->when($search, function ($query, $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('user_temp.user_name', 'LIKE', "%{$search}%")
                    ->orWhere('user_temp.full_name', 'LIKE', "%{$search}%")
                    ->orWhere('user_temp.email', 'LIKE', "%{$search}%");
                });
            })
            ->when($filter->has('indentity_status') && $filter->indentity_status != null, function ($q) use ($filter) {
                switch ($filter->indentity_status) {
                    case User::IDENTITY_PENDING_STATUS: $q->where('user_temp.identity_status', User::IDENTITY_PENDING_STATUS);
                        break;
                    case User::IDENTITY_PROCESS_STATUS: $q->where('user_temp.identity_status', User::IDENTITY_PROCESS_STATUS);
                        break;
                    case User::IDENTITY_ACCEPT_STATUS: $q->where('user_temp.identity_status', User::IDENTITY_ACCEPT_STATUS);
                        break;
                    case User::IDENTITY_REJECT_STATUS: $q->where('user_temp.identity_status', User::IDENTITY_REJECT_STATUS);
                        break;
                    default:
                        break;
                }
            })
            ->orderBy('id');

        return $response;
    }

    /**
     * handle update black list acccout.
     */
    public function updateBlackListAccout($userId, $status)
    {
        if ($status) {
            return $this->model->where('id', $userId)
                        ->update([
                            'state' => User::USER_BLACKLIST,
                        ]);
        } else {
            return $this->model->where('id', $userId)
                        ->update([
                            'state' => User::USER_ACTIVE,
                        ]);
        }
    }

    public function sendMailConfirm($email, $userName)
    {
        $token = app('crypt')->encode($email.';'.Carbon::now()->addMinutes(15));
        $url = url('api/auth/verify-register/'.$token);
        Mail::to($email)->send(new VerifyRegisterUser(compact('userName', 'url')));

        return true;
    }

    /**
     * get list user active.
     */
    public function getListUserActive($column = ['*'])
    {
        return $this->model->where('state', User::USER_ACTIVE)->select($column);
    }

    /**
     * get user detail.
     */
    public function getDetaiUserById($userId)
    {
        return $this->model->where('id', $userId)
                        ->with('region:id,state_name')
                        ->with('store:user_id,store_address')
                        ->first();
    }

    /**
     * check status identify card is process.
     */
    public function checkIdentiryCardIsProcess($userId)
    {
        return $this->model->where('id', $userId)
                        ->where('identity_status', User::IDENTITY_PROCESS_STATUS)
                        ->exists();
    }

    /**
     * check status identify card is approve.
     */
    public function checkIdentiryCardIsApprove($userId)
    {
        return $this->model->where('id', $userId)
                        ->where('identity_status', User::IDENTITY_ACCEPT_STATUS)
                        ->exists();
    }

    /**
     * get user by phone already vefiried.
     */
    public function getUserByPhoneVerified($phone)
    {
        return $this->model->where('phone', $phone)->where('is_phone_verified', 1)->first();
    }

    /**
     * get user active by array user_id.
     */
    public function getUserActive(array $userIds)
    {
        return $this->model->whereIn('id', $userIds)
                        ->where('state', User::USER_ACTIVE)
                        ->pluck('id');
    }

    /**
     * updateSwitchNoticesProgress
     *
     * @param  int $userId
     * @param  int $type
     * @return bool
     */
    public function updateSwitchNoticesProgress($userId, $type)
    {
        $this->model->where('id', $userId)
                        ->update([
                            'switch_notices_progress' => $type
                        ]);
        return true;
    }

    /**
     * Handle reset phone verify
     */
    public function resetPhoneHandle($userId)
    {
        $this->model->where('id', $userId)
                ->update([
                    'phone' => null,
                    'phone_verify' => null,
                    'phone_verify_token'=>null,
                    'is_phone_verified'=>0,
                    'is_two_fa'=>0,
                ]);
        return true;
    }
}
