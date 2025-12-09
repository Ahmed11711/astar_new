<?php

namespace App\Http\Controllers\Admin\StudentRegistrations;

use App\Repositories\StudentRegistrations\StudentRegistrationsRepositoryInterface;
use App\Http\Controllers\BaseController\BaseController;
use App\Http\Requests\Admin\StudentRegistrations\StudentRegistrationsStoreRequest;
use App\Http\Requests\Admin\StudentRegistrations\StudentRegistrationsUpdateRequest;
use App\Http\Resources\Admin\StudentRegistrations\StudentRegistrationsResource;

class StudentRegistrationsController extends BaseController
{
    public function __construct(StudentRegistrationsRepositoryInterface $repository)
    {
        parent::__construct();

        $this->initService(
            repository: $repository,
            collectionName: 'StudentRegistrations'
        );

        $this->storeRequestClass = StudentRegistrationsStoreRequest::class;
        $this->updateRequestClass = StudentRegistrationsUpdateRequest::class;
        $this->resourceClass = StudentRegistrationsResource::class;
    }
}
