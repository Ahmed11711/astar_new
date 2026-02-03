<?php

namespace App\Repositories\HeroSection;

use App\Repositories\HeroSection\HeroSectionRepositoryInterface;
use App\Repositories\BaseRepository\BaseRepository;
use App\Models\HeroSection;

class HeroSectionRepository extends BaseRepository implements HeroSectionRepositoryInterface
{
    public function __construct(HeroSection $model)
    {
        parent::__construct($model);
    }
}
