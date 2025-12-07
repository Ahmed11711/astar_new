<?php

namespace App\Http\Controllers\Admin\setting;

use App\Repositories\setting\settingRepositoryInterface;
use App\Http\Controllers\BaseController\BaseController;
use App\Http\Requests\Admin\setting\settingStoreRequest;
use App\Http\Requests\Admin\setting\settingUpdateRequest;
use App\Http\Resources\Admin\setting\settingResource;

class settingController extends BaseController
{
    public function __construct(settingRepositoryInterface $repository)
    {
        parent::__construct();

        $this->initService(
            repository: $repository,
            collectionName: 'setting'
        );

        $this->storeRequestClass = settingStoreRequest::class;
        $this->updateRequestClass = settingUpdateRequest::class;
        $this->resourceClass = settingResource::class;
    }
}
