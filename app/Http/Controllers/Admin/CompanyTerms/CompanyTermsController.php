<?php

namespace App\Http\Controllers\Admin\CompanyTerms;

use Illuminate\Http\Request;
use App\Constant\StatusConstant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Helpers\General\CollectionHelper;
use App\Repositories\CompanyTermsRepository;
use App\Http\Requests\Admin\CreateCompanyTermsRequest;
use App\Http\Requests\Admin\DestroyCompanyTermsRequest;

/**
 * Class CompanyTermsController.
 *
 * @package namespace App\Http\Controllers;
 */
class CompanyTermsController extends Controller
{
    /**
     * @var CompanyTermsRepository
     */
    protected $repository;

    /**
     * CompanyTermsController constructor.
     *
     * @param CompanyTermsRepository $repository
     */
    public function __construct(CompanyTermsRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * get index
     */
    public function index(Request $request)
    {
        try {
            $type = $request->type ?? null;
            $pagination = $request->has('per_page') && is_numeric($request->per_page) ? $request->per_page : 10;
            $data = $this->repository->findWhere(['type' => $type])->sortByDesc('id');
            $data = CollectionHelper::paginate($data, $pagination);

            return response()->json([
                'success' => true,
                'data' => $data
            ]);
        } catch (\Throwable $th) {
            Log::error('Controllers\Admin\CompanyTerms\CompanyTermsController - index : '.$th->getMessage());
            return response()->json([
                'error' => true,
                'message' => __('api.exception'),
            ]);
        }
    }

    /**
     * create company terms
     */
    public function createCompanyTerms(CreateCompanyTermsRequest $request)
    {
        try {
            DB::beginTransaction();
            if ($request->hasfile('file_name')) {
                $name = $request->file_name->getClientOriginalName();
                $imagePath = uploadPDFToS3($request->file_name, config('setting.folder.company_terms'));
                $data = [
                    'file_name' => $name,
                    'file_path' => $imagePath,
                    'file_size' => $request->file_name->getSize(),
                    'type' => $request->type,
                ];
            }
            $data = $this->repository->create($data);
            DB::commit();

            return response()->json([
                'success' => true,
                'data' => $data
            ]);
        } catch (\Throwable $th) {
            Log::error('Controllers\Admin\CompanyTerms\CompanyTermsController - createCompanyTerms : '.$th->getMessage());
            DB::rollback();
            return response()->json([
                'error' => true,
                'message' => __('api.exception'),
            ]);
        }
    }

    /**
     * destroy
     */
    public function destroy(DestroyCompanyTermsRequest $request)
    {
        try {
            DB::beginTransaction();
            foreach ($request->id as $val) {
                $terms = $this->repository->find($val);
                removeFileS3($terms->file_path);
                $this->repository->delete($val);
            }
            DB::commit();
            return response()->json([
                'success' => true,
                'message' => null,
            ]);
        } catch (\Throwable $th) {
            Log::error('Controllers\Admin\CompanyTerms\CompanyTermsController - destroy : '.$th->getMessage());
            DB::rollback();
            return response()->json([
                'error' => true,
                'message' => __('api.exception'),
            ]);
        }
    }
}
