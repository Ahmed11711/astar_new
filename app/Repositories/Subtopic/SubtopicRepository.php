<?php

namespace App\Repositories\Subtopic;

use App\Repositories\Subtopic\SubtopicRepositoryInterface;
use App\Repositories\BaseRepository\BaseRepository;
use App\Models\Subtopic;

class SubtopicRepository extends BaseRepository implements SubtopicRepositoryInterface
{
    public function __construct(Subtopic $model)
    {
        parent::__construct($model);
    }
}
