<?php

namespace App\Repositories\successStories;

use App\Repositories\successStories\successStoriesRepositoryInterface;
use App\Repositories\BaseRepository\BaseRepository;
use App\Models\successStories;

class successStoriesRepository extends BaseRepository implements successStoriesRepositoryInterface
{
    public function __construct(successStories $model)
    {
        parent::__construct($model);
    }
}
