<?php

namespace App\Http\Controllers\Api\Question;

use App\Http\Controllers\Api\ApiController;
use App\Repositories\QuestionRepository;

/**
 * Class QuestionsController.
 */
class QuestionsController extends ApiController
{
    /**
     * @var QuestionRepository
     */
    protected $repository;

    /**
     * QuestionsController constructor.
     */
    public function __construct(QuestionRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->repository->pushCriteria(app('Prettus\Repository\Criteria\RequestCriteria'));
        $questions = $this->repository->with('answers')->all();

        return response()->json([
            'success' => true,
            'data' => $questions,
        ]);
    }
}
