<?php

namespace App\Http\Controllers\School\AllTeacher;

use \App\Models\StudentAssignment;
use App\Http\Controllers\Controller;
use App\Http\Service\School\myTeacherService;
use App\Models\ExamPaper;
use App\Models\StudentSubject;
use App\Models\User;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

class AllTeacherController extends Controller
{
    use ApiResponseTrait;

    public function __construct(public myTeacherService $myTeacherService) {}

    public function allTeachers(Request $request)
    {
        $limit = $request->query('limit', 10);

        $teacherIds = $this->myTeacherService->getMyTeachers($request->user_id);

        $teachers = User::query()
            ->select('users.id', 'users.username', 'users.email')
            ->with([
                'grades:id,name',
                'subjects:id,name',
            ])
            ->withCount([
                'createdExams as by_create',
                'students as students_count'
            ])
            ->whereIn('users.id', $teacherIds)
            ->paginate($limit);

        return $this->successResponsePaginate($teachers);
    }


    public function dashboard(Request $request)
    {
        $schoolId = $request->user_id;

        $teacherIds = $this->myTeacherService->getMyTeachers($schoolId);

        $data = [
            'total_teachers' => count($teacherIds),

            'total_students' => StudentAssignment::whereIn(
                'assigned_id',
                $teacherIds
            )
                ->distinct('student_id')
                ->count('student_id'),


            'total_subjects' =>  StudentSubject::whereIn(
                'student_id',
                $teacherIds
            )->count(),

            'total_exams' => ExamPaper::whereIn(
                'created_by',
                $teacherIds
            )->count(),


        ];

        return $this->successResponse($data);
    }
}
