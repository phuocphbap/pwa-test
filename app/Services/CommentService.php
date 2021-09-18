<?php

namespace App\Services;

use App\Repositories\CommentRepository;
use App\Repositories\ServiceRepository;

class CommentService
{
    protected $commentRepo;
    protected $serviceRepo;

    public function __construct(CommentRepository $commentRepo, ServiceRepository $serviceRepo)
    {
        $this->commentRepo = $commentRepo;
        $this->serviceRepo = $serviceRepo;
    }

    /**
     * get service
     */
    public function getOwnerServiceByComment($serviceId)
    {
        $service = $this->serviceRepo->find($serviceId);
        $userId = $service->store->user->id;
        $nameService = $service->service_title;

        return [
            'userId' => $userId,
            'nameService' => $nameService,
        ];
    }
}
