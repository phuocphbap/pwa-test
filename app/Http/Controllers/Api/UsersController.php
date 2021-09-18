<?php

namespace App\Http\Controllers\Api;

use App\Constant\StatusConstant;
use App\Helpers\Facades\UploadS3Helper;
use App\Http\Requests\CreateRequestWithdrawRequest;
use App\Http\Requests\IdentityCardCreateRequest;
use App\Http\Requests\LeaveGroupRequest;
use App\Http\Requests\UpdateTwoFaRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Repositories\ReferralBonusRepository;
use App\Repositories\UserRepository;
use App\Services\FirebaseService;
use App\Services\UserService;
use App\Services\WithdrawService;
use Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Prettus\Validator\Exceptions\ValidatorException;
use Throwable;

/**
 * Class UsersController.
 */
class UsersController extends Controller
{
    /**
     * @var UserRepository
     */
    protected $repository;

    /**
     * @var UserService
     */
    protected $userService;

    /**
     * @var ReferralBonusRepository
     */
    protected $referralBonusRepository;

    /**
     * @var WithdrawService
     */
    protected $withdrawService;

    /**
     * @var FirebaseService
     */
    protected $firebaseService;

    /**
     * UsersController constructor.
     */
    public function __construct(
        UserRepository $repository,
        UserService $userService,
        WithdrawService $withdrawService,
        FirebaseService $firebaseService,
        ReferralBonusRepository $referralBonusRepository
    ) {
        $this->repository = $repository;
        $this->userService = $userService;
        $this->withdrawService = $withdrawService;
        $this->firebaseService = $firebaseService;
        $this->referralBonusRepository = $referralBonusRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->repository->pushCriteria(app('Prettus\Repository\Criteria\RequestCriteria'));
        $users = $this->repository->where('state', 1)->paginate(10);

        return response()->json([
            'status' => true,
            'data' => $users,
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = $this->repository->getDetailUser($id);
        if ($user) {
            return response()->json([
                'success' => true,
                'data' => $user,
            ]);
        } else {
            return response()->json([
                'error' => true,
                'message' => __('api.common.not_found'),
            ]);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param string $id
     *
     * @return Response
     *
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function update(UserUpdateRequest $request, $id)
    {
        try {
            $user = $this->repository->find($id);
            $data = $request->only(['user_name', 'phone', 'gender', 'detail', 'first_name', 'last_name', 'birth_date', 'store_address']);
            if ($request->avatar) {
                //delete old avatar
                if ($user->avatar) {
                    UploadS3Helper::deleteImage($user->avatar);
                }
                $imageLink = UploadS3Helper::uploadImage($request->avatar, 'avatar');

                $data['avatar'] = $imageLink;
            } else {
                $data['avatar'] = $user->avatar;
            }
            $status = $this->repository->updateProfile($data, $id);
            if ($status) {
                return response()->json([
                    'success' => true,
                    'message' => __('api.common.update_success'),
                ]);
            }

            return response()->json([
                'error' => true,
                'message' => __('api.common.failed'),
            ]);
        } catch (ValidatorException $e) {
            return response()->json([
                'error' => true,
                'message' => __('api.common.failed'),
            ]);
        }
    }

    public function updateProfile(UserUpdateRequest $request)
    {
        try {
            DB::beginTransaction();
            $user = auth()->user();
            $data = $request->only(['email', 'user_name', 'user_phone', 'gender', 'detail', 'first_name', 'last_name', 'birth_date', 'user_address', 'is_kyc_profiled']);
            if (!$user->email) {
                $data['email'] = $request->email;
            }
            if($request->user_address) {
                $data['address'] = $request->user_address;;
            }
            if($request->user_phone) {
                $data['phone'] = $request->user_phone;
            }
            if (isset($data['email'])) {
                if ($user->is_email_verified == 1) {
                    return response()->json(['error' => true, 'message' => __('api.common.failed')]);
                }
            }
            if ($request->avatar) {
                //delete old avatar
                if ($user->avatar) {
                    UploadS3Helper::deleteImage($user->avatar);
                }
                $imageLink = UploadS3Helper::uploadImage($request->avatar, 'avatar');

                $data['avatar'] = $imageLink;
            } else {
                $data['avatar'] = $user->avatar;
            }

            $this->repository->updateProfile($data, $user->id);
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => __('api.common.update_success'),
            ]);
        } catch (Throwable $th) {
            Log::error('Controllers\Api\UsersController - updateProfile : '.$th->getMessage());
            DB::rollBack();

            return response()->json([
                'error' => true,
                'message' => __('api.common.failed'),
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $is_two_fa
     *
     * @return \Illuminate\Http\Response
     */

    public function resetPhone()
    {
        try {
            $user = auth()->user();
            $this->repository->resetPhoneHandle($user->id);
            return response()->json([
                'success' => true,
                'message' => __('api.common.update_success'),
            ]);
        } catch (Throwable $th) {
            Log::error('Controllers\Api\UsersController - updateProfile : '.$th->getMessage());
            return response()->json([
                'error' => true,
                'message' => __('api.common.failed'),
            ]);
        }
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param int $is_two_fa
     *
     * @return \Illuminate\Http\Response
     */

    public function updateTwoFa(UpdateTwoFaRequest $request)
    {
        try {
            $user = auth()->user();
            $data['is_two_fa'] = $request->is_two_fa;
            $this->repository->update($data, $user->id);
            return response()->json([
                'success' => true,
                'message' => __('api.common.update_success'),
            ]);
        } catch (Throwable $th) {
            Log::error('Controllers\Api\UsersController - updateProfile : '.$th->getMessage());
            return response()->json([
                'error' => true,
                'message' => __('api.common.failed'),
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $deleted = $this->repository->delete($id);

        return response()->json([
            'success' => true,
            'message' => __('api.common.deleted'),
        ]);
    }

    public function getPointInWallet()
    {
        $userId = auth()->user()->id;
        $walletAmount = $this->repository->getPointOfUser($userId);
        $walletAmountExpire = $this->repository->getWalletExpire($userId);
        $ammount_expire = $walletAmountExpire ? $walletAmountExpire->amount : 0;
        $expire_date = $walletAmountExpire ? $walletAmountExpire->expire_date : null;
        return response()->json([
            'success' => true,
            'data' => [
                'total' => $walletAmount + $ammount_expire,
                'amount' => $walletAmount,
                'amount_expire' => $ammount_expire,
                'expire_date' => $expire_date,
            ],
        ]);
    }

    /**
     * update identity card for user.
     *
     * @return \Illuminate\Http\Response
     */
    public function updateIdentityCard(IdentityCardCreateRequest $request)
    {
        try {
            DB::beginTransaction();
            $userId = auth()->user()->id;
            $checkID = $this->userService->checkIdentifyCard($userId);
            if ($checkID) {
                $this->userService->removeIdentityCard($userId);
            }

            $dataRes = $this->repository->updateIdentityCard($userId, $request->images);
            if ($dataRes) {
                DB::commit();

                return response()->json([
                    'success' => true,
                    'data' => $dataRes,
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => __('api.common.failed'),
            ]);
        } catch (\Throwable $th) {
            Log::error('Controllers\Api\UsersController - updateIdentity : '.$th->getMessage());
            DB::rollBack();

            return response()->json([
                'error' => true,
                'message' => __('api.exception'),
            ]);
        }
    }

    /**
     * handle leave group.
     */
    public function leaveGroup(LeaveGroupRequest $request)
    {
        try {
            DB::beginTransaction();
            $user = auth()->user();
            //check password
            if (!Hash::check($request->password, $user->password)) {
                return response()->json(['error' => true, 'message' => __('api.user.password_incorect')]);
            }
            // check progress
            $checkProgress = $this->userService->checkExistsProgress($user->id);
            if ($checkProgress) {
                return response()->json(['error' => true, 'message' => __('api.validation.progress_not_yet_finish')]);
            }

            // check user had services
            $checkService = $user->store->services;
            if ($checkService->isNotEmpty()) {
                return response()->json(['error' => true, 'message' => __('api.validation.services_is_selling')]);
            }

            // check user pendding withdraw
            $checkWithDraw = $this->withdrawService->checkExistsStatusWithDraw($user->id);
            if ($checkWithDraw) {
                return response()->json(['error' => true, 'message' => __('api.validation.user_is_pending_withdraw')]);
            }

            // handle leave chat
            $this->userService->handleLeaveChat($user->id, true, StatusConstant::USER_LEAVE_GROUP);
            $this->repository->updateLeaveGroup($user->id, $request->reason);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => __('api.leave_group.leave_success'),
            ]);
        } catch (\Throwable $th) {
            Log::error('Controllers\Api\UsersController - leaveGroup : '.$th->getMessage());
            DB::rollBack();

            return response()->json([
                'error' => true,
                'message' => __('api.exception'),
            ]);
        }
    }

    /**
     * Request withdraw of user.
     */
    public function requestWithDraw(CreateRequestWithdrawRequest $request)
    {
        try {
            DB::beginTransaction();
            if (!auth()->user()->bankAccount) {
                return response()->json([
                    'error' => true,
                    'message' => __('api.bank_account.account_not_exists'),
                ]);
            }

            $pointValid = $this->repository->getPointOfUser(auth()->user()->id);
            if ($pointValid < $request->amount) {
                return response()->json([
                    'error' => true,
                    'message' => __('api.payment.point_not_enough'),
                ]);
            }
            $dataRes = $this->withdrawService->requestWithdrawPoint($request->amount);

            // create chatbot
            $text = '出金申請を受け付けました。\n承認結果をお待ちください。';
            $room = $this->firebaseService->createRoomChatRequestWithdraw(auth()->user()->id);
            $this->firebaseService->createChatbotWithdraw($room->key_firebase, $text);
            $this->firebaseService->createLastMessageWithdraw(auth()->user()->id, $room->key_firebase, $text);

            // create notices
            $this->firebaseService->noticeRequestWithDraw(auth()->user());
            DB::commit();

            return response()->json([
                'success' => true,
                'data' => $dataRes,
            ]);
        } catch (\Throwable $th) {
            Log::error('Controllers\Api\UsersController - request withdrwaw : '.$th->getMessage());
            DB::rollBack();

            return response()->json([
                'error' => true,
                'message' => __('api.common.failed'),
            ]);
        }
    }

    /**
     * Reset avatar of user.
     */
    public function resetAvatar()
    {
        try {
            DB::beginTransaction();
            $user = auth()->user();
            if ($user->avatar) {
                UploadS3Helper::deleteImage($user->avatar);
                $user->update(['avatar' => null]);
            }
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => __('api.common.update_success'),
            ]);
        } catch (Throwable $th) {
            DB::rollBack();

            return response()->json([
                'error' => true,
                'message' => __('api.common.failed'),
            ]);
        }
    }

    /**
     * Check show referral code.
     */
    public function isShowReferralCode()
    {
        $data = $this->referralBonusRepository->getReferralBonus();
        if (!$data || $data->amount == 0) {
            return response()->json(['isShow' => false]);
        }

        return response()->json(['isShow' => true]);
    }
}
