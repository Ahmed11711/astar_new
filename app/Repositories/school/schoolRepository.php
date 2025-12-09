<?php

namespace App\Repositories\school;

use App\Repositories\school\schoolRepositoryInterface;
use App\Repositories\BaseRepository\BaseRepository;
use App\Models\school;

class schoolRepository extends BaseRepository implements schoolRepositoryInterface
{
    public function __construct(school $model)
    {
        parent::__construct($model);
    }
}
