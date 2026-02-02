<?php

namespace App\Http\Controllers\Teacher\bugs;

use App\Http\Controllers\Controller;
use App\Http\Requests\Teacher\bugsRequest;
use App\Http\Requests\Teacher\ReportTeacherRequest;
use App\Models\Report;
use App\Models\StudentAssignment;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

class ReportTeacherController extends Controller
{
    use ApiResponseTrait;
    public function index(Request $request)
    {
        $userId = $request->user_id;
        $bugs = Report::where('teacher_id', $userId)
            ->latest()
            ->get();

        return $this->successResponse($bugs, "");
    }

    public function store(ReportTeacherRequest $request)
    {
        $userId = $request->user_id;
        $teacherAssigned = StudentAssignment::where('student_id', $userId)->first();
        if (!$teacherAssigned) {
            $type = "admin";
        } else {
            $type = $teacherAssigned->assigned_id;
        }
        $bug = Report::create([
            'teacher_id'  => $userId,
            'title' => $request->title,
            'description' => $request->description,
            'type' => $type,
        ]);
        return $this->successResponse($bug, "");
    }
}
