<?php

namespace App\Http\Controllers\Teacher\MyExame;

use App\Http\Controllers\Controller;
use App\Http\Requests\Teacher\StudentQuizeRequest;
use App\Models\ExamPaper;
use App\Models\StudentAttamp;
use Illuminate\Http\Request;

class MyExameTeacherController extends Controller
{
    public function teacherExamsDashboard(Request $request)
    {
        $teacherId = $request->user_id;
        $teacherId = $request->user_id;

        // 1️⃣ جلب بيانات الأوراق (Papers)
        $papers = ExamPaper::where('created_by', $teacherId)
            ->leftJoin('student_attempts', function ($join) {
                $join->on('student_attempts.exam_id', '=', 'exam_papers.id');
            })
            ->selectRaw('
        exam_papers.id AS paper_id,
        exam_papers.title AS paper_title,
        COUNT(student_attempts.user_id) AS students_count,
        COALESCE(SUM(student_attempts.score),0) AS total_score,
        COALESCE(AVG(student_attempts.score),0) AS avg_score,
        COALESCE(SUM(student_attempts.time_remaining),0) AS total_time,
        COALESCE(AVG(student_attempts.time_remaining),0) AS avg_time
    ')
            ->groupBy('exam_papers.id', 'exam_papers.title')
            ->get();

        // 2️⃣ جلب بيانات الـ subjects المرتبطة بنفس الـ join
        $subjects = StudentAttamp::whereIn('exam_id', $papers->pluck('paper_id'))
            ->select('exam_id', 'subject_id', 'score', 'time_remaining') // أو أي أعمدة عايزها
            ->get();

        // 3️⃣ دمجهم في JSON واحد
        return response()->json([
            'papers' => $papers,
            'subjects' => $subjects,
        ]);
    }
}
