<?php

namespace App\Repositories\policy;

use App\Repositories\policy\policyRepositoryInterface;
use App\Repositories\BaseRepository\BaseRepository;
use App\Models\policy;

class policyRepository extends BaseRepository implements policyRepositoryInterface
{
    public function __construct(policy $model)
    {
        parent::__construct($model);
    }
}
