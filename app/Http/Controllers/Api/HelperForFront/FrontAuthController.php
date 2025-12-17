<?php

namespace App\Http\Controllers\Api\HelperForFront;

use App\Http\Controllers\Controller;
use App\Http\Service\HelperService\UserRoleService;
use App\Models\grade;
use App\Models\Packages;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

class FrontAuthController extends Controller
{
    use ApiResponseTrait;
    public function getGrades(Request $request)
    {
        $grades = Grade::with([
            'subjects:id,name,grade_id',
            'subjects.topics:id,name,subject_id'
        ])->get();
        return $this->successResponse($grades, "Grades with Subjects");
    }

    public function allTeacherAndSchool(Request $request, UserRoleService $service)
    {
        $result = $service->getTeachersAndSchools($request->id);

        if ($request->filled('id')) {
            if (!$result) {
                return $this->errorResponse('User not found', 404);
            }

            return $this->successResponse($result, 'User retrieved successfully');
        }

        return $this->successResponse([
            'teachers' => $result->get('teacher', []),
            'schools'  => $result->get('school', []),
        ], 'Teachers and Schools retrieved successfully');
    }



    public function getPackageByAccount(Request $request)
    {
        return Packages::forAccount($request->assign_id)->get();
    }
}
