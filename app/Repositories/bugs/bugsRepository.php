<?php

namespace App\Repositories\bugs;

use App\Repositories\bugs\bugsRepositoryInterface;
use App\Repositories\BaseRepository\BaseRepository;
use App\Models\bugs;

class bugsRepository extends BaseRepository implements bugsRepositoryInterface
{
    public function __construct(bugs $model)
    {
        parent::__construct($model);
    }
}
