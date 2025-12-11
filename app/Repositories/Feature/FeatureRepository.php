<?php

namespace App\Repositories\Feature;

use App\Repositories\Feature\FeatureRepositoryInterface;
use App\Repositories\BaseRepository\BaseRepository;
use App\Models\Feature;

class FeatureRepository extends BaseRepository implements FeatureRepositoryInterface
{
    public function __construct(Feature $model)
    {
        parent::__construct($model);
    }
}
