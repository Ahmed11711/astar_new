<?php

namespace App\Repositories\grade;

use App\Repositories\grade\gradeRepositoryInterface;
use App\Repositories\BaseRepository\BaseRepository;
use App\Models\grade;

class gradeRepository extends BaseRepository implements gradeRepositoryInterface
{
    public function __construct(grade $model)
    {
        parent::__construct($model);
    }
}
