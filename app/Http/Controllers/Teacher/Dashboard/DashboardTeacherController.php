<?php

namespace App\Http\Controllers\Teacher\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardTeacherController extends Controller
{
    public function index(Request $request)
    {
        $teacherId = $request->user_id;

        // 1️⃣ بيانات الورق ومتوسط الدرجات per user
        $papers = DB::table('exam_papers')
            ->joinSub(
                DB::table('student_attempts')
                    ->select(
                        'exam_id',
                        'paper_id',
                        'user_id',
                        DB::raw('AVG(score) as student_avg_score')
                    )
                    ->groupBy('exam_id', 'paper_id', 'user_id'),
                'student_avg',
                function ($join) {
                    $join->on('exam_papers.id', '=', 'student_avg.exam_id')
                        ->on('exam_papers.paper_id', '=', 'student_avg.paper_id');
                }
            )
            ->where('exam_papers.created_by', $teacherId)
            ->select(
                'exam_papers.paper_id',
                'exam_papers.title',
                DB::raw('AVG(student_avg.student_avg_score) as average_score')
            )
            ->groupBy('exam_papers.paper_id', 'exam_papers.title')
            ->get();

        // 2️⃣ عدد كل المحاولات لكل الورق
        $total_attempts = DB::table('exam_papers')
            ->join('student_attempts', function ($join) {
                $join->on('exam_papers.id', '=', 'student_attempts.exam_id')
                    ->on('exam_papers.paper_id', '=', 'student_attempts.paper_id');
            })
            ->where('exam_papers.created_by', $teacherId)
            ->count('student_attempts.id');

        // 3️⃣ عدد كل الصفوف في student_assignments (user_id) بدون أي شرط
        $total_users = DB::table('student_assignments')
            ->where('assigned_id', $teacherId)
            ->count('student_id');

        // 4️⃣ رجع كل حاجة في array واحدة
        return [
            'papers' => $papers,
            'NumberOfExamsSolved' => $total_attempts,
            'total_users' => $total_users
        ];
    }
}
