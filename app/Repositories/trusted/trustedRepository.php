<?php

namespace App\Repositories\trusted;

use App\Repositories\trusted\trustedRepositoryInterface;
use App\Repositories\BaseRepository\BaseRepository;
use App\Models\trusted;

class trustedRepository extends BaseRepository implements trustedRepositoryInterface
{
    public function __construct(trusted $model)
    {
        parent::__construct($model);
    }
}
