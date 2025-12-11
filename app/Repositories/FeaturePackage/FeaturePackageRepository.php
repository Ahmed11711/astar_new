<?php

namespace App\Repositories\FeaturePackage;

use App\Repositories\FeaturePackage\FeaturePackageRepositoryInterface;
use App\Repositories\BaseRepository\BaseRepository;
use App\Models\FeaturePackage;

class FeaturePackageRepository extends BaseRepository implements FeaturePackageRepositoryInterface
{
    public function __construct(FeaturePackage $model)
    {
        parent::__construct($model);
    }
}
