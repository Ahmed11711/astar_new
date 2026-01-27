<?php

namespace App\Http\Controllers\Teacher\bugs;

use App\Http\Controllers\Controller;
use App\Http\Requests\Teacher\bugsRequest;
use App\Http\Requests\Teacher\ReportTeacherRequest;
use App\Models\Report;
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
        $bug = Report::create([
            'teacher_id'  => $userId,
            'title' => $request->title,
            'description' => $request->description,
        ]);
        return $this->successResponse($bug, "");
    }
}
