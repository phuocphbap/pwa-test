<?php

namespace App\Http\Controllers\Admin\Withdraw;

use App\Entities\WithdrawRequest;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\WithdrawUpdateStatusRequest;
use App\Http\Requests\GetHistoryWithDrawRequest;
use App\Repositories\WithdrawRequestRepository;
use App\Services\Admin\NotificationsService;
use App\Services\Admin\WithdrawService;
use App\Services\FirebaseService;
use DB;
use Illuminate\Support\Facades\Log;

class WithDrawController extends Controller
{
    /**
     * @var WithdrawRequestRepository
     */
    protected $repository;
    protected $withdrawService;
    protected $noticeService;
    protected $firebaseService;

    /**
     * WithDrawController constructor.
     */
    public function __construct(
        WithdrawRequestRepository $repository,
        WithdrawService $withdrawService,
        NotificationsService $noticeService,
        FirebaseService $firebaseService
    ) {
        $this->repository = $repository;
        $this->withdrawService = $withdrawService;
        $this->noticeService = $noticeService;
        $this->firebaseService = $firebaseService;
    }

    /**
     * get history withdraw.
     */
    public function index(GetHistoryWithDrawRequest $request)
    {
        try {
            $filter = newCond([
                'start_date' => $request->start_date ?: '',
                'end_date' => $request->end_date ?: '',
                'state' => $request->state ?: WithdrawRequest::PENDING_STATE,
            ]);

            $pagination = $request->has('per_page') && is_numeric($request->per_page) ? $request->per_page : 10;
            $data = $this->repository->listWithdrawInAdmin($filter)->paginate($pagination);

            return response()->json([
                'success' => true,
                'data' => $data,
            ]);
        } catch (\Throwable $th) {
            Log::error('Controllers\Api\WithDraw\WithDrawController - withdraw:admin-index : '.$th->getMessage());

            return response()->json([
                'error' => true,
                'message' => __('api.exception'),
            ]);
        }
    }

    public function updateStatusWithdraw(WithdrawUpdateStatusRequest $request)
    {
        try {
            DB::beginTransaction();
            $withdrawData = $this->repository->find($request->withdrawId);
            switch ($request->status) {
                case WithdrawRequest::ACCEPTED_STATE:
                    if ($withdrawData->state != WithdrawRequest::PENDING_STATE) {
                        return response()->json(['error' => true, 'message' => __('api.exception')]);
                    }
                    $withdrawData->update([
                        'state' => WithdrawRequest::ACCEPTED_STATE,
                        'date_accepted' => \Carbon\Carbon::now(),
                    ]);

                    // create chatbot
                    $text = '出金が承認されました。\n振込予定日は翌月10日（土日祝日の場合は、直前の営業日）となります。';
                    $this->handleSendMessageWithdraw($text, $withdrawData->user_id);
                    // create notices
                    $this->noticeService->noticeWithDrawPoint($withdrawData->user_id, WithdrawRequest::ACCEPTED_STATE);
                    DB::commit();

                    break;
                case WithdrawRequest::REJECTED_STATE:
                        if ($withdrawData->state == WithdrawRequest::PENDING_STATE || $withdrawData->state == WithdrawRequest::ACCEPTED_STATE) {
                            //refunds point to wallet of user
                            $this->withdrawService->rejectWithDrawPoint($withdrawData->id);

                            $withdrawData->update([
                                'state' => WithdrawRequest::REJECTED_STATE,
                                'date_rejected' => \Carbon\Carbon::now(),
                                'reason_rejected' => $request->reason_rejected,
                            ]);
                            // create chatbot
                            $text = '出金が却下されました。理由は' . $request->reason_rejected;
                            $this->handleSendMessageWithdraw($text, $withdrawData->user_id);
                            // push notices
                            $this->noticeService->noticeWithDrawPoint($withdrawData->user_id, WithdrawRequest::REJECTED_STATE, $request->reason_rejected);
                            DB::commit();
                        } else {
                            return response()->json(['error' => true, 'message' => __('api.exception')]);
                        }

                    break;
                case WithdrawRequest::DONE_STATE:
                    if ($withdrawData->state != WithdrawRequest::ACCEPTED_STATE) {
                        return response()->json(['error' => true, 'message' => __('api.exception')]);
                    }
                    $withdrawData->update([
                        'state' => WithdrawRequest::DONE_STATE,
                    ]);
                    // create chatbot
                    $text = '前月の出金申請金額の振り込みを完了しました。';
                    $this->handleSendMessageWithdraw($text, $withdrawData->user_id);
                    // push notices
                    $this->noticeService->noticeWithDrawPoint($withdrawData->user_id, WithdrawRequest::DONE_STATE);
                    DB::commit();
                    break;
                default:
                    return response()->json(['error' => true, 'message' => __('api.exception')]);
                    break;
            }

            return response()->json([
                'success' => true,
                'data' => $withdrawData,
                'message' => __('api.withdraw.update_status_success'),
            ]);
        } catch (\Throwable $th) {
            Log::error('Controllers\Api\WithDraw\WithDrawController - updateStatusWithdraw: '.$th->getMessage());
            DB::rollback();

            return response()->json([
                'error' => true,
                'message' => __('api.exception'),
            ]);
        }
    }
    
    /**
     * handleSendMessageWithdraw
     *
     * @param string $text
     * @param int $userId
     *
     * @return void
     */
    public function handleSendMessageWithdraw(string $text, int $userId)
    {
        try {
            $room = $this->firebaseService->createRoomChatRequestWithdraw($userId);
            $this->firebaseService->createChatbotWithdraw($room->key_firebase, $text);
            $this->firebaseService->createLastMessageWithdraw($userId, $room->key_firebase, $text);

            return true;
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
