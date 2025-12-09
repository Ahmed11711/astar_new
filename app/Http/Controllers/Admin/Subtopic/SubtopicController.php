<?php

namespace App\Http\Controllers\Admin\Subtopic;

use App\Repositories\Subtopic\SubtopicRepositoryInterface;
use App\Http\Controllers\BaseController\BaseController;
use App\Http\Requests\Admin\Subtopic\SubtopicStoreRequest;
use App\Http\Requests\Admin\Subtopic\SubtopicUpdateRequest;
use App\Http\Resources\Admin\Subtopic\SubtopicResource;

class SubtopicController extends BaseController
{
    public function __construct(SubtopicRepositoryInterface $repository)
    {
        parent::__construct();

        $this->initService(
            repository: $repository,
            collectionName: 'Subtopic'
        );

        $this->storeRequestClass = SubtopicStoreRequest::class;
        $this->updateRequestClass = SubtopicUpdateRequest::class;
        $this->resourceClass = SubtopicResource::class;
    }
}
