<?php

namespace App\Http\Controllers\Admin\trusted;

use App\Repositories\trusted\trustedRepositoryInterface;
use App\Http\Controllers\BaseController\BaseController;
use App\Http\Requests\Admin\trusted\trustedStoreRequest;
use App\Http\Requests\Admin\trusted\trustedUpdateRequest;
use App\Http\Resources\Admin\trusted\trustedResource;

class trustedController extends BaseController
{
    public function __construct(trustedRepositoryInterface $repository)
    {
        parent::__construct();

        $this->initService(
            repository: $repository,
            collectionName: 'trusted'
        );

        $this->storeRequestClass = trustedStoreRequest::class;
        $this->updateRequestClass = trustedUpdateRequest::class;
        $this->resourceClass = trustedResource::class;
    }
}
