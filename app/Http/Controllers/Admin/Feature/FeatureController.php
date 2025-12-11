<?php

namespace App\Http\Controllers\Admin\Feature;

use App\Repositories\Feature\FeatureRepositoryInterface;
use App\Http\Controllers\BaseController\BaseController;
use App\Http\Requests\Admin\Feature\FeatureStoreRequest;
use App\Http\Requests\Admin\Feature\FeatureUpdateRequest;
use App\Http\Resources\Admin\Feature\FeatureResource;

class FeatureController extends BaseController
{
    public function __construct(FeatureRepositoryInterface $repository)
    {
        parent::__construct();

        $this->initService(
            repository: $repository,
            collectionName: 'Feature'
        );

        $this->storeRequestClass = FeatureStoreRequest::class;
        $this->updateRequestClass = FeatureUpdateRequest::class;
        $this->resourceClass = FeatureResource::class;
    }
}
