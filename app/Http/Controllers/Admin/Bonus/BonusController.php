<?php

namespace App\Http\Controllers\Admin\Bonus;

use App\Helpers\General\CollectionHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\BonusIndecatedRequest;
use App\Http\Requests\Admin\CreateBonusRequest;
use App\Http\Requests\Admin\VerifyIdentifyCardRequest;
use App\Repositories\BonusRepository;
use App\Repositories\ReferralBonusRepository;
use App\Services\Admin\BonusService;
use App\Services\Admin\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Class BonusesController.
 */
class BonusController extends Controller
{
    /**
     * @var ReferralBonusRepository
     */
    protected $repository;

    /**
     * @var BonusRepository
     */
    protected $bonusRepository;

    /**
     * @var BonusService
     */
    protected $bonusService;
    /**
     * @var UserService
     */
    protected $userService;

    /**
     * BonusesController constructor.
     */
    public function __construct(
        ReferralBonusRepository $repository,
        BonusService $bonusService,
        BonusRepository $bonusRepository,
        UserService $userService
    ) {
        $this->repository = $repository;
        $this->bonusService = $bonusService;
        $this->userService = $userService;
        $this->bonusRepository = $bonusRepository;
    }

    public function listBonusesInAdmin(Request $request)
    {
        try {
            $pagination = $request->has('per_page') && is_numeric($request->per_page) ? $request->per_page : 10;
            $data = $this->bonusRepository->listBonusesInAdmin($request->type);

            $data = CollectionHelper::paginate($data, $pagination);

            return response()->json([
                'success' => true,
                'data' => $data,
            ]);
        } catch (\Throwable $th) {
            Log::error('Controllers\Admin\Bonus\BonusController - getListBonuses : '.$th->getMessage());

            return response()->json([
                'error' => true,
                'message' => __('api.exception'),
            ]);
        }
    }

    /**
     * create point.
     */
    public function createBonus(CreateBonusRequest $request)
    {
        try {
            DB::beginTransaction();
            $bonus = $this->repository->all();
            if ($bonus->isNotEmpty()) {
                foreach ($bonus as $key => $value) {
                    $this->repository->delete($value->id);
                }
            }
            $id = $this->repository->create($request->all())->id;
            DB::commit();

            return response()->json([
                'success' => true,
                'data' => $id,
            ]);
        } catch (\Throwable $th) {
            Log::error('Controllers\Admin\Bonus\BonusController - createBonus : '.$th->getMessage());
            DB::rollback();

            return response()->json([
                'error' => true,
                'message' => __('api.exception'),
            ]);
        }
    }

    /**
     * get point bonus.
     */
    public function getPointBonus(Request $request)
    {
        try {
            $data = $this->repository->first('amount');

            return response()->json([
                'success' => true,
                'data' => $data,
            ]);
        } catch (\Throwable $th) {
            Log::error('Controllers\Admin\Bonus\BonusController - getPointBonus : '.$th->getMessage());

            return response()->json([
                'error' => true,
                'message' => __('api.exception'),
            ]);
        }
    }

    /**
     * bonus indecated for user.
     */
    public function handleBonusIndecated(BonusIndecatedRequest $request)
    {
        try {
            DB::beginTransaction();
            if ($request->check_all) {
                $this->bonusService->handleBonusToAllUser($request->amount);
            } else {
                if (empty($request->user_id)) {
                    return response()->json([
                        'error' => true,
                        'message' => __('api.validation.user_id_required'),
                    ]);
                }
                $this->bonusService->handleBonusIndecated($request->user_id, $request->amount);
            }
            DB::commit();

            return response()->json([
                'success' => true,
                'data' => null,
            ]);
        } catch (\Throwable $th) {
            Log::error('Controllers\Admin\Bonus\BonusController - handleBonusIndecated : '.$th->getMessage());
            DB::rollback();

            return response()->json([
                'error' => true,
                'message' => __('api.exception'),
            ]);
        }
    }

    /**
     * handle verify indentity card.
     */
    public function verifyIdentifyCard(VerifyIdentifyCardRequest $request)
    {
        try {
            DB::beginTransaction();
            $checkID = $this->userService->checkIdentifyCard($request->userId);
            if (!$checkID) {
                return response()->json(['error' => true, 'message' => __('api.identity_card.identity_card_not_exits')]);
            }
            $checkProcess = $this->bonusService->checkIdentiryCardIsProcess($request->userId);
            if (!$checkProcess) {
                return response()->json(['error' => true, 'message' => __('api.identity_card.identity_card_not_process')]);
            }

            $this->bonusService->handleVerifyIdentityCard($request->userId, $request->type);
            DB::commit();

            return response()->json([
                'success' => true,
                'data' => null,
            ]);
        } catch (\Throwable $th) {
            Log::error('Controllers\Admin\Bonus\BonusController - verifyIdentifyCard : '.$th->getMessage());
            DB::rollback();

            return response()->json([
                'error' => true,
                'message' => __('api.exception'),
            ]);
        }
    }
}
