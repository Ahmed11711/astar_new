<?php

namespace App\Http\Controllers\Admin\school;

use App\Repositories\school\schoolRepositoryInterface;
use App\Http\Controllers\BaseController\BaseController;
use App\Http\Requests\Admin\school\schoolStoreRequest;
use App\Http\Requests\Admin\school\schoolUpdateRequest;
use App\Http\Resources\Admin\school\schoolResource;

class schoolController extends BaseController
{
    public function __construct(schoolRepositoryInterface $repository)
    {
        parent::__construct();

        $this->initService(
            repository: $repository,
            collectionName: 'school'
        );

        $this->storeRequestClass = schoolStoreRequest::class;
        $this->updateRequestClass = schoolUpdateRequest::class;
        $this->resourceClass = schoolResource::class;
    }
}
