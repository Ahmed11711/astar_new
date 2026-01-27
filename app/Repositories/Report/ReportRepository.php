<?php

namespace App\Repositories\Report;

use App\Repositories\Report\ReportRepositoryInterface;
use App\Repositories\BaseRepository\BaseRepository;
use App\Models\Report;

class ReportRepository extends BaseRepository implements ReportRepositoryInterface
{
    public function __construct(Report $model)
    {
        parent::__construct($model);
    }
}
