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
use Illuminate\Support\Facades\DB;

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

        $studentIds = StudentAssignment::whereIn('assigned_id', $teacherIds)
            ->distinct()
            ->pluck('student_id');

        $subjectsWithStudentsCount = DB::table('student_subject')
            ->join('subjects', 'subjects.id', '=', 'student_subject.subject_id')
            ->whereIn('student_subject.student_id', $studentIds)
            ->select(
                'subjects.id',
                'subjects.name',
                DB::raw('COUNT(DISTINCT student_subject.student_id) as students_count')
            )
            ->groupBy('subjects.id', 'subjects.name')
            ->get();

        $data = [
            'total_teachers' => count($teacherIds),

            'total_students' => $studentIds->count(),

            'total_subjects' => $subjectsWithStudentsCount->count(),

            'total_exams' => ExamPaper::whereIn('created_by', $teacherIds)->count(),

            'subjects' => $subjectsWithStudentsCount,
        ];

        return $this->successResponse($data);
    }
}
