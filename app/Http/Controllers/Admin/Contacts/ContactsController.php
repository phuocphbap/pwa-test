<?php

namespace App\Http\Controllers\Admin\Contacts;

use Carbon\Carbon;
use App\Mail\ContactMail;
use Illuminate\Http\Request;
use App\Constant\StatusConstant;
use Illuminate\Support\Facades\DB;
use App\Services\Admin\UserService;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use App\Repositories\ContactRepository;
use App\Helpers\General\CollectionHelper;
use App\Services\Admin\NotificationsService;
use App\Http\Requests\Admin\AnswerContactRequest;

class ContactsController extends Controller
{
    /**
     * @var ContactRepository
     */
    protected $repository;

    /**
     * @var UserService
     */
    protected $userService;

    /**
     * @var NotificationsService
     */
    protected $noticeService;

    /**
     * ContactsController constructor.
     */
    public function __construct(ContactRepository $repository, UserService $userService, NotificationsService $noticeService)
    {
        $this->repository = $repository;
        $this->userService = $userService;
        $this->noticeService = $noticeService;
    }

    /**
     * get list contact
     */
    public function getListContact(Request $request)
    {
        try {
            $pagination = $request->has('per_page') && is_numeric($request->per_page) ? $request->per_page : 10;
            $data = $this->repository->getListContact()->paginate($pagination);

            return response()->json([
                'success' => true,
                'data' => $data
            ]);
        } catch (\Throwable $th) {
            Log::error('Controllers\Admin\Contacts\ContactsController - getListContact : '.$th->getMessage());
            return response()->json([
                'error' => true,
                'message' => __('api.exception'),
            ]);
        }
    }

    /**
     * answer contact
     */
    public function answerContact(AnswerContactRequest $request)
    {
        try {
            $contacter = $this->repository->find($request->id);
            $details = [
                'question' => $contacter->contents,
                'answer' => $request->answer,
            ];

            if (!$contacter->email) {
                return response()->json(['error' => true, 'message' => __('api.contact.email_not_exists')]);
            }

            Mail::to($contacter->email)->send(new ContactMail($details));

            $this->repository->update([
                'state' => StatusConstant::CONTACT_EMAIL_ANSWERED,
                'reply_contents' => $request->answer,
                'reply_time' => Carbon::now()->toDateTimeString(),
            ], $contacter->id);

            // push notification
            $user = $this->userService->getUserByEmail($contacter->email);
            if ($user) {
                $this->noticeService->noticesContact($user, $contacter->contents);
            }

            return response()->json([
                'success' => true,
                'data' => null,
            ]);
        } catch (\Throwable $th) {
            Log::error('Controllers\Admin\Contacts\ContactsController - answerContact : '.$th->getMessage());
            return response()->json([
                'error' => true,
                'message' => __('api.exception'),
            ]);
        }
    }

    /**
     * delete contact
     */
    public function deleteContact($id)
    {
        try {
            $contact = $this->repository->find($id);
            if (!$contact) {
                return response()->json(['error' => true, 'message' => __('api.common.id_not_exists')]);
            }
            $this->repository->delete($id);

            return response()->json([
                'success' => true,
                'data' => null,
            ]);
        } catch (\Throwable $th) {
            Log::error('Controllers\Admin\Contacts\ContactsController - answerContact : '.$th->getMessage());
            return response()->json([
                'error' => true,
                'message' => __('api.exception'),
            ]);
        }
    }
}
