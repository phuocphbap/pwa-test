<?php

namespace App\Services\Admin;

use App\Constant\StatusConstant;
use App\Repositories\BankAccountRepository;
use App\Repositories\ChatRepository;
use App\Repositories\CommentRepository;
use App\Repositories\IdentityCardRepository;
use App\Repositories\RequestConsultingRepository;
use App\Repositories\ServiceRepository;
use App\Repositories\ServiceReviewRepository;
use App\Repositories\StoreRepository;
use App\Repositories\UserRepository;
use App\Repositories\WithdrawRequestRepository;
use App\Services\FirebaseService;
use Illuminate\Support\Facades\DB;

class UserService
{
    protected $userRepo;
    protected $firebaseService;
    protected $IDRepo;
    protected $bankAccRepo;
    protected $serviceRepo;
    protected $storeRepo;
    protected $consultingRepo;
    protected $commentRepo;
    protected $reviewRepo;
    protected $chatRepo;
    protected $withdrawRepository;

    /**
     * constructor.
     */
    public function __construct(
        UserRepository $userRepo,
        FirebaseService $firebaseService,
        IdentityCardRepository $IDRepo,
        BankAccountRepository $bankAccRepo,
        ServiceRepository $serviceRepo,
        StoreRepository $storeRepo,
        RequestConsultingRepository $consultingRepo,
        CommentRepository $commentRepo,
        ServiceReviewRepository $reviewRepo,
        ChatRepository $chatRepo,
        WithdrawRequestRepository $withdrawRepository
    ) {
        $this->userRepo = $userRepo;
        $this->firebaseService = $firebaseService;
        $this->IDRepo = $IDRepo;
        $this->bankAccRepo = $bankAccRepo;
        $this->serviceRepo = $serviceRepo;
        $this->storeRepo = $storeRepo;
        $this->consultingRepo = $consultingRepo;
        $this->commentRepo = $commentRepo;
        $this->reviewRepo = $reviewRepo;
        $this->chatRepo = $chatRepo;
        $this->withdrawRepository = $withdrawRepository;
    }

    /**
     * handle black list account.
     */
    public function handleBlackListAccount($userId, $type)
    {
        switch ($type) {
            case StatusConstant::USER_IS_NOT_BLACK_LIST:
                $this->userRepo->updateBlackListAccout($userId, true);
                // logout user
                DB::table('oauth_access_tokens')->where('user_id', $userId)->delete();
                $this->firebaseService->handleLeaveChat($userId, true, StatusConstant::USER_BLACKLIST);

                return true;
                break;
            case StatusConstant::USER_IS_BLACK_LIST:
                $this->userRepo->updateBlackListAccout($userId, false);
                $this->firebaseService->handleLeaveChat($userId, false, StatusConstant::USER_BLACKLIST);

                return true;
                break;
            default:
                // code...
                break;
        }
    }

    /**
     * get detail identify card.
     */
    public function getDetailIdentifyCard($userId)
    {
        return $this->IDRepo->getDetailIDByUserId($userId);
    }

    /**
     * get detail bank account by user_id.
     */
    public function getDetailBankAccount($userId)
    {
        return $this->bankAccRepo->getBankAccountByUserId($userId);
    }

    /**
     * get services by user_id.
     */
    public function getServiceByUserId($userId)
    {
        return $this->serviceRepo->getServiceByUserId($userId);
    }

    /**
     * get progress list by user_id.
     */
    public function getProgressListByService($userId, $progessType, $serviceId)
    {
        return $this->consultingRepo->getConsultingByService($userId, $progessType, $serviceId);
    }

    /**
     * get progress list by user_id.
     */
    public function getProgressListByUser($userId, $type, $progessType)
    {
        switch ($type) {
            case StatusConstant::PROGRESS_ARE_RECEIVING:
                return $this->consultingRepo->getConsultingByCustomerId($userId, $progessType);
                break;
            case StatusConstant::PROGRESS_ARE_PROVIDING:
                return $this->consultingRepo->getConsultingByOwnerId($userId, $progessType);
                break;
            default:
                break;
        }
    }

    /**
     * get detail service.
     */
    public function getDetailService($serviceId)
    {
        return $this->serviceRepo->getServiceDetail($serviceId);
    }

    /**
     * get comment service by service_id.
     */
    public function getCommentService($serviceId)
    {
        return $this->commentRepo->where('service_id', $serviceId)
                        ->with('user:id,email,user_name,avatar')
                        ->get();
    }

    /**
     * get review service by service_id.
     */
    public function getReviewService($serviceId)
    {
        return $this->reviewRepo->with('user:id,email,user_name,avatar')
                        ->where('service_id', $serviceId)
                        ->where('is_owner', 0)
                        ->get();
    }

    /**
     * get related service.
     */
    public function getRelatedService($serviceId)
    {
        $service = $this->serviceRepo->find($serviceId);
        $categoryId = $service->category_id;

        return $this->serviceRepo->getRelatedtServices($serviceId, $categoryId);
    }

    /**
     * get room chat by consulting_id.
     */
    public function getProgressChat($consultingId)
    {
        return $this->chatRepo->getRoomChatByConsulting($consultingId);
    }

    /**
     * check Identify Card.
     */
    public function checkIdentifyCard($userId)
    {
        return $this->IDRepo->checkIdentifyCard($userId);
    }

    /**
     * get detail progress.
     */
    public function getDetailProgress($consultingId)
    {
        return $this->consultingRepo->getDetailProgress($consultingId);
    }

    public function checkExistsProgressBlackList($userId)
    {
        return $this->consultingRepo->checkExistsProgress($userId);
    }

    public function checkExistsStatusWithDrawAdmin($userId)
    {
        return $this->withdrawRepository->checkExistsStatusWithDraw($userId);
    }

    /**
     * get user by email.
     */
    public function getUserByEmail($email)
    {
        return $this->userRepo->findWhere(['email' => $email])->first();
    }

    public function getListChatExceptRequestConsulting($userId, $search)
    {
        return $this->chatRepo->getListChatExceptRequestConsulting($userId, $search);
    }
}
