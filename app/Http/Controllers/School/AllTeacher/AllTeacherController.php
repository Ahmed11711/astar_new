<?php

namespace App\Http\Controllers\School\AllTeacher;

use App\Http\Controllers\Controller;
use App\Http\Service\School\myTeacherService;
use App\Models\User;
use Illuminate\Http\Request;

class AllTeacherController extends Controller
{
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
                'createdExams as by_create'
            ])
            ->whereIn('users.id', $teacherIds)
            ->paginate($limit);

        return response()->json([
            'status' => 'success',
            'data' => $teachers,
        ]);
    }
}
