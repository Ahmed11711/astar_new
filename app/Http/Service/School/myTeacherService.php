<?php

namespace App\Http\Service\School;

use App\Models\User;
use App\Models\UserPackageFeature;
use Illuminate\Support\Facades\DB;
use App\Models\Packages;
use App\Models\StudentAssignment;

class myTeacherService
{
    public function getMyTeachers($userId)
    {
        return  $teachers = StudentAssignment::where('assigned_id', $userId)
            ->pluck('student_id')
            ->toArray();
    }
}
