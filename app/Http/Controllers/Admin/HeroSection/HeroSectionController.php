<?php

namespace App\Http\Controllers\Admin\HeroSection;

use App\Repositories\HeroSection\HeroSectionRepositoryInterface;
use App\Http\Controllers\BaseController\BaseController;
use App\Http\Requests\Admin\HeroSection\HeroSectionStoreRequest;
use App\Http\Requests\Admin\HeroSection\HeroSectionUpdateRequest;
use App\Http\Resources\Admin\HeroSection\HeroSectionResource;

class HeroSectionController extends BaseController
{
    public function __construct(HeroSectionRepositoryInterface $repository)
    {
        parent::__construct();

        $this->initService(
            repository: $repository,
            collectionName: 'HeroSection',
            fileFields: ['background_image']
        );

        $this->storeRequestClass = HeroSectionStoreRequest::class;
        $this->updateRequestClass = HeroSectionUpdateRequest::class;
        $this->resourceClass = HeroSectionResource::class;
    }
}
