<?php

namespace App\Http\Controllers\Admin\policy;

use App\Repositories\policy\policyRepositoryInterface;
use App\Http\Controllers\BaseController\BaseController;
use App\Http\Requests\Admin\policy\policyStoreRequest;
use App\Http\Requests\Admin\policy\policyUpdateRequest;
use App\Http\Resources\Admin\policy\policyResource;

class policyController extends BaseController
{
    public function __construct(policyRepositoryInterface $repository)
    {
        parent::__construct();

        $this->initService(
            repository: $repository,
            collectionName: 'policy'
        );

        $this->storeRequestClass = policyStoreRequest::class;
        $this->updateRequestClass = policyUpdateRequest::class;
        $this->resourceClass = policyResource::class;
    }
}
