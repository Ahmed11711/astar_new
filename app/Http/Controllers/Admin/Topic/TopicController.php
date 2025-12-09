<?php

namespace App\Http\Controllers\Admin\Topic;

use App\Repositories\Topic\TopicRepositoryInterface;
use App\Http\Controllers\BaseController\BaseController;
use App\Http\Requests\Admin\Topic\TopicStoreRequest;
use App\Http\Requests\Admin\Topic\TopicUpdateRequest;
use App\Http\Resources\Admin\Topic\TopicResource;

class TopicController extends BaseController
{
    public function __construct(TopicRepositoryInterface $repository)
    {
        parent::__construct();

        $this->initService(
            repository: $repository,
            collectionName: 'Topic'
        );

        $this->storeRequestClass = TopicStoreRequest::class;
        $this->updateRequestClass = TopicUpdateRequest::class;
        $this->resourceClass = TopicResource::class;
    }
}
