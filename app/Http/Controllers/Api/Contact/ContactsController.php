<?php

namespace App\Http\Controllers\Api\Contact;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\ContactCreateRequest;
use App\Repositories\ContactRepository;
use Prettus\Validator\Exceptions\ValidatorException;

/**
 * Class ContactsController.
 */
class ContactsController extends ApiController
{
    /**
     * @var ContactRepository
     */
    protected $repository;

    /**
     * ContactsController constructor.
     */
    public function __construct(ContactRepository $repository)
    {
        $this->repository = $repository;
    }

    public function store(ContactCreateRequest $request)
    {
        try {
            $data = $request->only('name', 'email', 'contents', 'phone');
            $contact = $this->repository->create($data);

            return response()->json([
                'success' => true,
                'data' => $contact,
            ]);
        } catch (ValidatorException $e) {
            return response()->json([
                'error' => true,
                'message' => __('api.exception'),
            ]);
        }
    }
}
