<?php

namespace App\Http\Controllers\Admin\successStories;

use App\Repositories\successStories\successStoriesRepositoryInterface;
use App\Http\Controllers\BaseController\BaseController;
use App\Http\Requests\Admin\successStories\successStoriesStoreRequest;
use App\Http\Requests\Admin\successStories\successStoriesUpdateRequest;
use App\Http\Resources\Admin\successStories\successStoriesResource;

class successStoriesController extends BaseController
{
    public function __construct(successStoriesRepositoryInterface $repository)
    {
        parent::__construct();

        $this->initService(
            repository: $repository,
            collectionName: 'successStories',
        );

        $this->storeRequestClass = successStoriesStoreRequest::class;
        $this->updateRequestClass = successStoriesUpdateRequest::class;
        $this->resourceClass = successStoriesResource::class;
    }
}
