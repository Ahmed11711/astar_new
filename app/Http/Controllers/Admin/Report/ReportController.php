<?php

namespace App\Http\Controllers\Admin\Report;

use App\Repositories\Report\ReportRepositoryInterface;
use App\Http\Controllers\BaseController\BaseController;
use App\Http\Requests\Admin\Report\ReportStoreRequest;
use App\Http\Requests\Admin\Report\ReportUpdateRequest;
use App\Http\Resources\Admin\Report\ReportResource;

class ReportController extends BaseController
{
    public function __construct(ReportRepositoryInterface $repository)
    {
        parent::__construct();

        $this->initService(
            repository: $repository,
            collectionName: 'Report'
        );

        $this->storeRequestClass = ReportStoreRequest::class;
        $this->updateRequestClass = ReportUpdateRequest::class;
        $this->resourceClass = ReportResource::class;
    }
}
