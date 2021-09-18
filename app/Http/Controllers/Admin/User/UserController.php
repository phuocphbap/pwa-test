<?php

namespace App\Http\Controllers\Admin\User;

use App\Helpers\General\CollectionHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\BlackListAccountRequest;
use App\Http\Requests\Admin\GetDetailServiceRequest;
use App\Http\Requests\Admin\GetDetailUserRequest;
use App\Http\Requests\Admin\GetListChatOfUserRequest;
use App\Http\Requests\Admin\GetProgressChatRequest;
use App\Http\Requests\Admin\GetProgressListUserRequest;
use App\Repositories\UserRepository;
use App\Services\Admin\NotificationsService;
use App\Services\Admin\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Class BonusesController.
 */
class UserController extends Controller
{
    /**
     * @var UserRepository
     */
    protected $repository;

    /**
     * @var UserService
     */

    /**
     * @var NotificationsService
     */
    protected $noticeService;

    /**
     * BonusesController constructor.
     */
    public function __construct(
        UserRepository $repository,
        UserService $userService,
        NotificationsService $noticeService
    ) {
        $this->repository = $repository;
        $this->userService = $userService;
        $this->noticeService = $noticeService;
    }

    /**
     * get list user.
     */
    public function getListUser(Request $request)
    {
        try {
            $filter = newCond([
                'search' => $request->search ?? null,
                'indentity_status' => $request->indentity_status ?? null,
            ]);
            $pagination = $request->has('per_page') && is_numeric($request->per_page) ? $request->per_page : 10;
            $data = $this->repository->getListUser($filter)->paginate($pagination);

            return response()->json([
                'success' => true,
                'data' => $data,
            ]);
        } catch (\Throwable $th) {
            Log::error('Controllers\Admin\User\UserController - getListUser : '.$th->getMessage());

            return response()->json([
                'error' => true,
                'message' => __('api.exception'),
            ]);
        }
    }

    /**
     * handle black list account.
     */
    public function handleBlackListAccount(BlackListAccountRequest $request)
    {
        try {
            DB::beginTransaction();
            // check progress
            $checkProgress = $this->userService->checkExistsProgressBlackList($request->userId);
            if ($checkProgress) {
                return response()->json(['error' => true, 'message' => __('api.validation.progress_not_yet_finish')]);
            }

            // check user pendding withdraw
            $checkWithDraw = $this->userService->checkExistsStatusWithDrawAdmin($request->userId);
            if ($checkWithDraw) {
                return response()->json(['error' => true, 'message' => __('api.validation.user_is_pending_withdraw')]);
            }

            $this->userService->handleBlackListAccount($request->userId, $request->type);
            DB::commit();

            return response()->json([
                'message' => __('api.user.account_updated'),
                'data' => null,
            ]);
        } catch (\Throwable $th) {
            Log::error('Controllers\Admin\User\UserController - handleBlackListAccount : '.$th->getMessage());
            DB::rollback();

            return response()->json([
                'error' => true,
                'message' => __('api.exception'),
            ]);
        }
    }

    /**
     * get detail user by id.
     */
    public function getDetailUser(GetDetailUserRequest $request)
    {
        try {
            $data = $this->repository->getDetaiUserById($request->userId);

            return response()->json([
                'success' => true,
                'data' => $data,
            ]);
        } catch (\Throwable $th) {
            Log::error('Controllers\Admin\User\UserController - getDetailUser : '.$th->getMessage());

            return response()->json([
                'error' => true,
                'message' => __('api.exception'),
            ]);
        }
    }

    /**
     * get identify card of user by id.
     */
    public function getIdentifyCard(GetDetailUserRequest $request)
    {
        try {
            $data = $this->userService->getDetailIdentifyCard($request->userId);

            return response()->json([
                'success' => true,
                'data' => $data,
            ]);
        } catch (\Throwable $th) {
            Log::error('Controllers\Admin\User\UserController - getIdentifyCard : '.$th->getMessage());

            return response()->json([
                'error' => true,
                'message' => __('api.exception'),
            ]);
        }
    }

    /**
     * get identify card of user by id.
     */
    public function getBankAccount(GetDetailUserRequest $request)
    {
        try {
            $data = $this->userService->getDetailBankAccount($request->userId);

            return response()->json([
                'success' => true,
                'data' => $data,
            ]);
        } catch (\Throwable $th) {
            Log::error('Controllers\Admin\User\UserController - getIdentifyCard : '.$th->getMessage());

            return response()->json([
                'error' => true,
                'message' => __('api.exception'),
            ]);
        }
    }

    /**
     * get services by user_id.
     */
    public function getServiceByUser(GetDetailUserRequest $request)
    {
        try {
            $pagination = $request->has('per_page') && is_numeric($request->per_page) ? $request->per_page : 10;
            $data = $this->userService->getServiceByUserId($request->userId)->paginate($pagination);

            return response()->json([
                'success' => true,
                'data' => $data,
            ]);
        } catch (\Throwable $th) {
            Log::error('Controllers\Admin\User\UserController - getServiceByUser : '.$th->getMessage());

            return response()->json([
                'error' => true,
                'message' => __('api.exception'),
            ]);
        }
    }

    /**
     * get point by user_id.
     */
    public function getPointByUser(GetDetailUserRequest $request)
    {
        try {
            $walletAmount = $this->repository->getPointOfUser($request->userId);
            $walletAmountExpire = $this->repository->getWalletExpire($request->userId);
            $ammount_expire = $walletAmountExpire->amount ?? 0;
            $expire_date = $walletAmountExpire->expire_date ?? null;

            return response()->json([
                'success' => true,
                'data' => [
                    'total' => $walletAmount + $ammount_expire,
                    'amount' => $walletAmount,
                    'amount_expire' => $ammount_expire,
                    'expire_date' => $expire_date,
                ],
            ]);
        } catch (\Throwable $th) {
            Log::error('Controllers\Admin\User\UserController - getPointByUser : '.$th->getMessage());

            return response()->json([
                'error' => true,
                'message' => __('api.exception'),
            ]);
        }
    }

    /**
     * get progress list user by user_id.
     */
    public function getProgressService(GetProgressListUserRequest $request)
    {
        // dd(123);
        try {
            $pagination = $request->has('per_page') && is_numeric($request->per_page) ? $request->per_page : 10;
            $data = $this->userService->getProgressListByService($request->userId, $request->progress_type, $request->serviceId);
            $data = CollectionHelper::paginate($data, $pagination);

            return response()->json([
                'success' => true,
                'data' => $data,
            ]);
        } catch (\Throwable $th) {
            Log::error('Controllers\Admin\User\UserController - getProgressListUser : '.$th->getMessage());

            return response()->json([
                'error' => true,
                'message' => __('api.exception'),
            ]);
        }
    }

    /**
     * get progress list user by user_id.
     */
    public function getProgressListUser(GetProgressListUserRequest $request)
    {
        try {
            $pagination = $request->has('per_page') && is_numeric($request->per_page) ? $request->per_page : 10;
            $data = $this->userService->getProgressListByUser($request->userId, $request->type, $request->progress_type);
            $data = CollectionHelper::paginate($data, $pagination);

            return response()->json([
                'success' => true,
                'data' => $data,
            ]);
        } catch (\Throwable $th) {
            Log::error('Controllers\Admin\User\UserController - getProgressListUser : '.$th->getMessage());

            return response()->json([
                'error' => true,
                'message' => __('api.exception'),
            ]);
        }
    }

    /**
     * get progress list user by service_id.
     */
    public function getDetailService(GetDetailServiceRequest $request)
    {
        try {
            $data = $this->userService->getDetailService($request->serviceId);

            return response()->json([
                'success' => true,
                'data' => $data,
            ]);
        } catch (\Throwable $th) {
            Log::error('Controllers\Admin\User\UserController - getDetailService : '.$th->getMessage());

            return response()->json([
                'error' => true,
                'message' => __('api.exception'),
            ]);
        }
    }

    /**
     * get comment service user by service_id.
     */
    public function getCommentService(GetDetailServiceRequest $request)
    {
        try {
            $pagination = $request->has('per_page') && is_numeric($request->per_page) ? $request->per_page : 10;
            $data = $this->userService->getCommentService($request->serviceId)->sortByDesc('created_at');
            $data = CollectionHelper::paginate($data, $pagination);

            return response()->json([
                'success' => true,
                'data' => $data,
            ]);
        } catch (\Throwable $th) {
            Log::error('Controllers\Admin\User\UserController - getCommentService : '.$th->getMessage());

            return response()->json([
                'error' => true,
                'message' => __('api.exception'),
            ]);
        }
    }

    /**
     * get reivew service user by service_id.
     */
    public function getReviewService(GetDetailServiceRequest $request)
    {
        try {
            $pagination = $request->has('per_page') && is_numeric($request->per_page) ? $request->per_page : 10;
            $data = $this->userService->getReviewService($request->serviceId)->sortByDesc('created_at');
            $data = CollectionHelper::paginate($data, $pagination);

            return response()->json([
                'success' => true,
                'data' => $data,
            ]);
        } catch (\Throwable $th) {
            Log::error('Controllers\Admin\User\UserController - getCommentService : '.$th->getMessage());

            return response()->json([
                'error' => true,
                'message' => __('api.exception'),
            ]);
        }
    }

    /**
     * get service related by category_id.
     */
    public function getRelatedService(GetDetailServiceRequest $request)
    {
        try {
            $pagination = $request->has('per_page') && is_numeric($request->per_page) ? $request->per_page : 10;
            $data = $this->userService->getRelatedService($request->serviceId)->paginate($pagination);

            return response()->json([
                'success' => true,
                'data' => $data,
            ]);
        } catch (\Throwable $th) {
            Log::error('Controllers\Admin\User\UserController - getRelatedService : '.$th->getMessage());

            return response()->json([
                'error' => true,
                'message' => __('api.exception'),
            ]);
        }
    }

    /**
     * get progress chat by consulting_id.
     */
    public function getProgressChat(GetProgressChatRequest $request)
    {
        try {
            $data = $this->userService->getProgressChat($request->consultingId);

            return response()->json([
                'success' => true,
                'data' => $data,
            ]);
        } catch (\Throwable $th) {
            Log::error('Controllers\Admin\User\UserController - getProgressChat : '.$th->getMessage());

            return response()->json([
                'error' => true,
                'message' => __('api.exception'),
            ]);
        }
    }

    /**
     * get detail progress.
     */
    public function getDetailProgress($consultingId)
    {
        try {
            $data = $this->userService->getDetailProgress($consultingId);

            return response()->json([
                'success' => true,
                'data' => $data,
            ]);
        } catch (\Throwable $th) {
            Log::error('Controllers\Admin\User\UserController - getDetailProgress : '.$th->getMessage());

            return response()->json([
                'error' => true,
                'message' => __('api.exception'),
            ]);
        }
    }

    /**
     * get progress chat by consulting_id.
     */
    public function getListChatExceptRequestConsulting(GetListChatOfUserRequest $request)
    {
        try {
            $pagination = $request->has('per_page') && is_numeric($request->per_page) ? $request->per_page : 10;

            $data = $this->userService->getListChatExceptRequestConsulting($request->userId, $request->search);
            $data = CollectionHelper::paginate($data, $pagination);

            return response()->json([
                'success' => true,
                'data' => $data,
            ]);
        } catch (\Throwable $th) {
            Log::error('Controllers\Admin\User\UserController - getListChatNotRequestConsulting : '.$th->getMessage());

            return response()->json([
                'error' => true,
                'message' => __('api.exception'),
            ]);
        }
    }

    /**
     * handle verify indentity card.
     */
    public function cancelAprovedIdentityCard(Request $request)
    {
        try {
            DB::beginTransaction();
            $checkID = $this->userService->checkIdentifyCard($request->userId);
            if (!$checkID) {
                return response()->json(['error' => true, 'message' => __('api.identity_card.identity_card_not_exits')]);
            }
            $checkApprove = $this->repository->checkIdentiryCardIsApprove($request->userId);
            if (!$checkApprove) {
                return response()->json(['error' => true, 'message' => __('api.identity_card.identity_card_not_process')]);
            }

            $this->repository->cancelAprovedIdentityCard($request->userId);
            $this->noticeService->noticeCancelApprovedIDCard($request->userId);
            DB::commit();

            return response()->json([
                'success' => true,
                'data' => null,
            ]);
        } catch (\Throwable $th) {
            Log::error('Controllers\Admin\Bonus\BonusController - cancelApproveIdentifyCard : '.$th->getMessage());
            DB::rollback();

            return response()->json([
                'error' => true,
                'message' => __('api.exception'),
            ]);
        }
    }

    public function listUserInputReferralCode(Request $request)
    {
        $user = $this->repository->find($request->userId);
        if (!$user) {
            return response()->json(['error' => true, 'message' => __('api.validation.user_id_not_exits')]);
        }
        $data = $this->repository->where('input_refferal_code', $user->referral_code)->orderBy('id')->paginate();

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    public function handleResetPhone(Request $request)
    {
        try {
            $user = $this->repository->find($request->userId);
            if (!$user) {
                return response()->json(['error' => true, 'message' => __('api.validation.user_id_not_exits')]);
            }
            //check phone verify
            if($user->is_phone_verified == 0) {
                return response()->json(['error' => true, 'message' => __('api.verify_phone.is_not_phone_verify')]);
            }
            $this->repository->resetPhoneHandle($request->userId);
            $this->noticeService->noticeResetPhone($request->userId);

            return response()->json([
                'message' => __('api.verify_phone.reset_phone_success'),
                'data' => null,
            ]);
        } catch (\Throwable $th) {
            Log::error('Controllers\Admin\User\UserController - handleResetPhone : '.$th->getMessage());
            return response()->json([
                'error' => true,
                'message' => __('api.exception'),
            ]);
        }

        
    }
}
