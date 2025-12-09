<?php

namespace App\Http\Controllers\Admin\Team;

use App\Repositories\Team\TeamRepositoryInterface;
use App\Http\Controllers\BaseController\BaseController;
use App\Http\Requests\Admin\Team\TeamStoreRequest;
use App\Http\Requests\Admin\Team\TeamUpdateRequest;
use App\Http\Resources\Admin\Team\TeamResource;

class TeamController extends BaseController
{
    public function __construct(TeamRepositoryInterface $repository)
    {
        parent::__construct();

        $this->initService(
            repository: $repository,
            collectionName: 'Team',
            fileFields: ['img']
        );

        $this->storeRequestClass = TeamStoreRequest::class;
        $this->updateRequestClass = TeamUpdateRequest::class;
        $this->resourceClass = TeamResource::class;
    }
}
