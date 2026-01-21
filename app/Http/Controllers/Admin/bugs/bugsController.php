<?php

namespace App\Http\Controllers\Admin\bugs;

use App\Repositories\bugs\bugsRepositoryInterface;
use App\Http\Controllers\BaseController\BaseController;
use App\Http\Requests\Admin\bugs\bugsStoreRequest;
use App\Http\Requests\Admin\bugs\bugsUpdateRequest;
use App\Http\Resources\Admin\bugs\bugsResource;

class bugsController extends BaseController
{
    public function __construct(bugsRepositoryInterface $repository)
    {
        parent::__construct();

        $this->initService(
            repository: $repository,
            collectionName: 'bugs'
        );

        $this->storeRequestClass = bugsStoreRequest::class;
        $this->updateRequestClass = bugsUpdateRequest::class;
        $this->resourceClass = bugsResource::class;
    }
}
