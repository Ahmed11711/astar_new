<?php

namespace App\Repositories\setting;

use App\Repositories\setting\settingRepositoryInterface;
use App\Repositories\BaseRepository\BaseRepository;
use App\Models\setting;

class settingRepository extends BaseRepository implements settingRepositoryInterface
{
    public function __construct(setting $model)
    {
        parent::__construct($model);
    }
}
