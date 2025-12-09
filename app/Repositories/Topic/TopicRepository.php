<?php

namespace App\Repositories\Topic;

use App\Repositories\Topic\TopicRepositoryInterface;
use App\Repositories\BaseRepository\BaseRepository;
use App\Models\Topic;

class TopicRepository extends BaseRepository implements TopicRepositoryInterface
{
    public function __construct(Topic $model)
    {
        parent::__construct($model);
    }
}
