<?php

namespace App\Repositories\Blog;

use App\Repositories\Blog\BlogRepositoryInterface;
use App\Repositories\BaseRepository\BaseRepository;
use App\Models\Blog;

class BlogRepository extends BaseRepository implements BlogRepositoryInterface
{
    public function __construct(Blog $model)
    {
        parent::__construct($model);
    }
}
