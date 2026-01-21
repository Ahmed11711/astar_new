<?php

namespace App\Http\Controllers\Teacher\MyExame;

use App\Http\Controllers\Controller;
use App\Http\Requests\Teacher\StudentQuizeRequest;
use App\Models\ExamPaper;
use Illuminate\Http\Request;

class MyExameTeacherController extends Controller
{
    public function teacherExamsDashboard(Request $request)
    {
        $teacherId = $request->user_id;

        return ExamPaper::where('created_by', $teacherId)
            ->leftJoin('student_attempts', function ($join) {
                $join->on('student_attempts.exam_id', '=', 'exam_papers.id');
            })
            ->selectRaw('
            exam_papers.id                          AS paper_id,
            exam_papers.title                       AS paper_title,

            COUNT(student_attempts.user_id)         AS students_count,

            COALESCE(SUM(student_attempts.score),0)           AS total_score,
            COALESCE(AVG(student_attempts.score),0)           AS avg_score,

            COALESCE(SUM(student_attempts.time_remaining),0)  AS total_time,
            COALESCE(AVG(student_attempts.time_remaining),0)  AS avg_time
        ')
            ->groupBy('exam_papers.id', 'exam_papers.title')
            ->get();
    }
}
