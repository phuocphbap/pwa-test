<?php

namespace App\Http\Controllers\Admin\Advertising;
//auth-test
//thopham
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CreateMediaAdvertisingRequest;
use App\Http\Requests\Admin\DestroyAdvertisingMediaRequest;
use App\Http\Requests\Admin\GetListBlockAdvertisingRequest;
use App\Http\Requests\Admin\StoreBlockContentRequest;
use App\Repositories\AdvertisingMediaRepository;
use App\Services\Admin\AdversitingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AdvertisingMediaController extends Controller
{
    /**
     * @var ReferralBonusRepository
     */
    protected $repository;

    /**
     * @var AdversitingService
     */
    protected $adsService;

    /**
     * BonusesController constructor.
     */
    public function __construct(AdvertisingMediaRepository $repository, AdversitingService $adsService)
    {
        $this->repository = $repository;
        $this->adsService = $adsService;
    }

    /**
     * get list category ads.
     */
    public function getListCategoryAds(Request $request)
    {
        try {
            $data = $this->adsService->getListCategoryAds();

            return response()->json([
                'success' => true,
                'data' => $data,
            ]);
        } catch (\Throwable $th) {
            Log::error('Controllers\Admin\Advertising\AdvertisingMediaController - getListBlockAdvertising : '.$th->getMessage());

            return response()->json([
                'error' => true,
                'message' => __('api.exception'),
            ]);
        }
    }

    /**
     * get list block.
     */
    public function getListBlockAdvertising(GetListBlockAdvertisingRequest $request)
    {
        try {
            $pagination = $request->has('per_page') && is_numeric($request->per_page) ? $request->per_page : 10;
            $data = $this->adsService->getListBlockAds($request->categoryId)->paginate($pagination);

            return response()->json([
                'success' => true,
                'data' => $data,
            ]);
        } catch (\Throwable $th) {
            Log::error('Controllers\Admin\Advertising\AdvertisingMediaController - getListBlockAdvertising : '.$th->getMessage());

            return response()->json([
                'error' => true,
                'message' => __('api.exception'),
            ]);
        }
    }

    /**
     * get list advertisin by block_id.
     *
     * @param int $blockId BlockId
     */
    public function index($blockId)
    {
        try {
            $data = $this->repository->findWhere(['block_id' => $blockId])->first();

            return response()->json([
                'success' => true,
                'data' => $data,
            ]);
        } catch (\Throwable $th) {
            Log::error('Controllers\Admin\Advertising\AdvertisingMediaController - getListBlockAdvertising : '.$th->getMessage());

            return response()->json([
                'error' => true,
                'message' => __('api.exception'),
            ]);
        }
    }

    /**
     * create media advertising.
     */
    public function createMediaAdvertising(CreateMediaAdvertisingRequest $request)
    {
        try {
            DB::beginTransaction();
            if ($request->hasfile('file_name')) {
                //delete old media
                $this->repository->where('block_id', $request->block_id)->delete();
                $imagePath = uploadFileToS3($request->file_name, config('setting.folder.advertising'));
                $request->merge([
                    'image_path' => $imagePath,
                ]);
            }
            $data = $this->repository->create($request->all());
            DB::commit();

            return response()->json([
                'success' => true,
                'data' => $data,
            ]);
        } catch (\Throwable $th) {
            Log::error('Controllers\Admin\Advertising\AdvertisingMediaController - createMediaAdvertising : '.$th->getMessage());
            DB::rollback();

            return response()->json([
                'error' => true,
                'message' => __('api.exception'),
            ]);
        }
    }

    /**
     * create media advertising.
     */
    public function destroy(DestroyAdvertisingMediaRequest $request)
    {
        try {
            DB::beginTransaction();
            $media = $this->repository->find($request->media_id);
            removeFileS3($media->image_path);
            $this->repository->delete($request->media_id);
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => null,
            ]);
        } catch (\Throwable $th) {
            Log::error('Controllers\Admin\Advertising\AdvertisingMediaController - destroy : '.$th->getMessage());
            DB::rollback();

            return response()->json([
                'error' => true,
                'message' => __('api.exception'),
            ]);
        }
    }

    /**
     * store block content.
     */
    public function storeBlockContent(StoreBlockContentRequest $request)
    {
        try {
            DB::beginTransaction();
            foreach ($request->all() as $data) {
                foreach ($data as $value) {
                    $this->adsService->updateContentBlock($value['block_id'], $value['contents']);
                }
            }
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => null,
            ]);
        } catch (\Throwable $th) {
            Log::error('Controllers\Admin\Advertising\AdvertisingMediaController - storeBlockContent : '.$th->getMessage());
            DB::rollback();

            return response()->json([
                'error' => true,
                'message' => __('api.exception'),
            ]);
        }
    }
}
