<?php

namespace App\Repositories\StudentRegistrations;

use App\Repositories\StudentRegistrations\StudentRegistrationsRepositoryInterface;
use App\Repositories\BaseRepository\BaseRepository;
use App\Models\StudentRegistrations;

class StudentRegistrationsRepository extends BaseRepository implements StudentRegistrationsRepositoryInterface
{
    public function __construct(StudentRegistrations $model)
    {
        parent::__construct($model);
    }
}
