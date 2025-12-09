<?php

namespace App\Http\Controllers\Admin\grade;

use App\Repositories\grade\gradeRepositoryInterface;
use App\Http\Controllers\BaseController\BaseController;
use App\Http\Requests\Admin\grade\gradeStoreRequest;
use App\Http\Requests\Admin\grade\gradeUpdateRequest;
use App\Http\Resources\Admin\grade\gradeResource;

class gradeController extends BaseController
{
    public function __construct(gradeRepositoryInterface $repository)
    {
        parent::__construct();

        $this->initService(
            repository: $repository,
            collectionName: 'grade'
        );

        $this->storeRequestClass = gradeStoreRequest::class;
        $this->updateRequestClass = gradeUpdateRequest::class;
        $this->resourceClass = gradeResource::class;
    }
}
