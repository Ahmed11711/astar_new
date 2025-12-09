<?php

namespace App\Repositories\Team;

use App\Repositories\Team\TeamRepositoryInterface;
use App\Repositories\BaseRepository\BaseRepository;
use App\Models\Team;

class TeamRepository extends BaseRepository implements TeamRepositoryInterface
{
    public function __construct(Team $model)
    {
        parent::__construct($model);
    }
}
