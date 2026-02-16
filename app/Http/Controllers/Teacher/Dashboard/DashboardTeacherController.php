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

        /*
        |----------------------------------------------------------------------
        | Sub Query: Average score per student per exam
        |----------------------------------------------------------------------
        */
        $studentAvgSubQuery = DB::table('student_attempts')
            ->select(
                'exam_id',
                'paper_id',
                'user_id',
                DB::raw('AVG(score) as student_avg_score')
            )
            ->groupBy('exam_id', 'paper_id', 'user_id');

        /*
        |----------------------------------------------------------------------
        | Papers Performance
        |----------------------------------------------------------------------
        */
        $papers = DB::table('exam_papers')
            ->joinSub($studentAvgSubQuery, 'student_avg', function ($join) {
                $join->on('exam_papers.id', '=', 'student_avg.exam_id')
                    ->on('exam_papers.paper_id', '=', 'student_avg.paper_id');
            })
            ->where('exam_papers.created_by', $teacherId)
            ->select(
                'exam_papers.paper_id',
                'exam_papers.title',
                DB::raw('AVG(student_avg.student_avg_score) as average_score')
            )
            ->groupBy('exam_papers.paper_id', 'exam_papers.title')
            ->get();

        /*
        |----------------------------------------------------------------------
        | Subjects Performance
        |----------------------------------------------------------------------
        */
        $subjects = DB::table('subjects')
            ->join('exam_papers', 'subjects.id', '=', 'exam_papers.subject_id')
            ->joinSub($studentAvgSubQuery, 'student_avg', function ($join) {
                $join->on('exam_papers.id', '=', 'student_avg.exam_id')
                    ->on('exam_papers.paper_id', '=', 'student_avg.paper_id');
            })
            ->where('exam_papers.created_by', $teacherId)
            ->select(
                'subjects.id',
                'subjects.name',
                DB::raw('AVG(student_avg.student_avg_score) as average_score')
            )
            ->groupBy('subjects.id', 'subjects.name')
            ->get();

        /*
        |----------------------------------------------------------------------
        | Quiz Performance
        |----------------------------------------------------------------------
        */
        $quizzes = DB::table('exam_papers')
            ->joinSub($studentAvgSubQuery, 'student_avg', function ($join) {
                $join->on('exam_papers.id', '=', 'student_avg.exam_id')
                    ->on('exam_papers.paper_id', '=', 'student_avg.paper_id');
            })
            ->where('exam_papers.created_by', $teacherId)
            ->where('exam_papers.exam_type', 'quiz')
            ->select(
                'exam_papers.paper_id',
                'exam_papers.title',
                DB::raw('AVG(student_avg.student_avg_score) as average_score')
            )
            ->groupBy('exam_papers.paper_id', 'exam_papers.title')
            ->get();

        /*
        |----------------------------------------------------------------------
        | Total Attempts & Total Students
        |----------------------------------------------------------------------
        */
        $totalAttempts = DB::table('exam_papers')
            ->join('student_attempts', function ($join) {
                $join->on('exam_papers.id', '=', 'student_attempts.exam_id')
                    ->on('exam_papers.paper_id', '=', 'student_attempts.paper_id');
            })
            ->where('exam_papers.created_by', $teacherId)
            ->count('student_attempts.id');

        $totalUsers = DB::table('student_assignments')
            ->where('assigned_id', $teacherId)
            ->distinct('student_id')
            ->count('student_id');

        /*
        |----------------------------------------------------------------------
        | Response
        |----------------------------------------------------------------------
        */
        return response()->json([
            'papers'   => $papers,
            'subjects' => $subjects,
            'quizzes'  => $quizzes,
            'number_of_exams_solved' => $totalAttempts,
            'total_users'          => $totalUsers,
        ]);
    }
}
