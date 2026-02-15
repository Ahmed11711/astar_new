<?php

namespace App\Http\Controllers\Student\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $userId     = $request->user_id;
        $subjectIds = $request->student_subject_ids;

        $from = $request->from
            ? Carbon::parse($request->from)->startOfDay()
            : now()->subWeek()->startOfDay();

        $to = $request->to
            ? Carbon::parse($request->to)->endOfDay()
            : now()->endOfDay();

        $period = CarbonPeriod::create($from, $to);

        /*
        |--------------------------------------------------------------------------
        | Query 1 — Subjects
        |--------------------------------------------------------------------------
        */
        $subjects = DB::table('subjects')
            ->whereIn('id', $subjectIds)
            ->select('id', 'name')
            ->get()
            ->keyBy('id');


        /*
        |--------------------------------------------------------------------------
        | Query 2 — Questions (NO JOIN)
        |--------------------------------------------------------------------------
        */
        $questions = DB::table('questions')
            ->whereIn('subject_id', $subjectIds)
            ->select(
                'id',
                'subject_id',
                'topic_id',
                'subtopics_id',
                'question_max_score'
            )
            ->get();



        $questionsBySubtopic = $questions->groupBy('subtopics_id');

        /*
        |--------------------------------------------------------------------------
        | Query 3 — Student Answers (GROUPED)
        |--------------------------------------------------------------------------
        */
        $answers = DB::table('answers')
            ->where('user_id', $userId)
            ->whereBetween('created_at', [$from, $to])
            ->select(
                'question_id',
                DB::raw('SUM(mark_score) as student_score')
            )
            ->groupBy('question_id')
            ->get()
            ->keyBy('question_id');


        /*
        |--------------------------------------------------------------------------
        | Query 4 — Daily Answers (Charts)
        |--------------------------------------------------------------------------
        */
        $dailyAnswers = DB::table('answers')
            ->where('user_id', $userId)
            ->whereBetween('created_at', [$from, $to])
            ->select(
                DB::raw('DATE(created_at) as day'),
                'question_id',
                DB::raw('COUNT(*) as answered')
            )
            ->groupBy('day', 'question_id')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Topics & Subtopics meta
        |--------------------------------------------------------------------------
        */
        $topics = DB::table('topics')
            ->whereIn('subject_id', $subjectIds)
            ->select('id', 'subject_id', 'name')
            ->get();

        $subtopics = DB::table('subtopics')
            ->select('id', 'topic_id', 'name')
            ->get()
            ->groupBy('topic_id');

        /*
        |--------------------------------------------------------------------------
        | Build Response
        |--------------------------------------------------------------------------
        */
        $subjectsData = $subjects->map(function ($subject) use (
            $topics,
            $subtopics,
            $questionsBySubtopic,
            $answers,
            $dailyAnswers,
            $period
        ) {

            $subjectTopics = $topics->where('subject_id', $subject->id);

            $topicsData = $subjectTopics->map(function ($topic) use (
                $subtopics,
                $questionsBySubtopic,
                $answers,
                $dailyAnswers,
                $period
            ) {

                $topicTotalQuestions = 0;
                $topicAnswered       = 0;
                $topicTotalMarks     = 0;
                $topicStudentMarks   = 0;

                $subtopicsData = collect($subtopics[$topic->id] ?? [])
                    ->map(function ($sub) use (
                        $questionsBySubtopic,
                        $answers,
                        $dailyAnswers,
                        $period,
                        &$topicTotalQuestions,
                        &$topicAnswered,
                        &$topicTotalMarks,
                        &$topicStudentMarks
                    ) {

                        $questions = $questionsBySubtopic[$sub->id] ?? collect();

                        $totalQuestions = $questions->count();
                        $totalMarks     = (int) $questions->sum('question_max_score');

                        $answeredQuestions = $questions
                            ->filter(fn($q) => isset($answers[$q->id]))
                            ->count();

                        $studentMarks = (int) $questions
                            ->sum(fn($q) => $answers[$q->id]->student_score ?? 0);

                        $topicTotalQuestions += $totalQuestions;
                        $topicAnswered       += $answeredQuestions;
                        $topicTotalMarks     += $totalMarks;
                        $topicStudentMarks   += $studentMarks;

                        /* Daily */
                        $cumulative = 0;
                        $daily = [];

                        foreach ($period as $date) {
                            $day = $date->format('Y-m-d');

                            $answeredToday = $dailyAnswers
                                ->where('day', $day)
                                ->whereIn('question_id', $questions->pluck('id'))
                                ->count();

                            $cumulative += $answeredToday;

                            $daily[] = [
                                'day'       => $day,
                                'answered'  => $answeredToday,
                                'remaining' => max($totalQuestions - $cumulative, 0),
                            ];
                        }

                        return [
                            'subtopic_name'       => $sub->name,
                            'total_questions'    => $totalQuestions,
                            'answered_questions' => $answeredQuestions,
                            'total_marks'        => $totalMarks,
                            'student_marks'      => $studentMarks,
                            'daily'              => $daily,
                        ];
                    })
                    ->values();

                return [
                    'topic_name'         => $topic->name,
                    'total_questions'    => $topicTotalQuestions,
                    'answered_questions' => $topicAnswered,
                    'total_marks'        => $topicTotalMarks,
                    'student_marks'      => $topicStudentMarks,
                    'subtopics'          => $subtopicsData,
                ];
            })->values();

            // return [
            //     'subject_id'               => $subject->id,
            //     'subject_name'             => $subject->name,
            //     'topics'                   => $topicsData,
            //     'subject_total_questions'  => $topicsData->sum('total_questions'),
            //     'subject_answered_questions' => $topicsData->sum('answered_questions'),
            //     'subject_total_marks'      => $topicsData->sum('total_marks'),
            //     'subject_student_marks'    => $topicsData->sum('student_marks'),
            // ];
            return [
                'subject_id'                => $subject->id,
                'subject_name'              => $subject->name,
                'topics'                    => $topicsData,
                'subject_total_questions'   => $topicsData->sum('total_questions'),
                'subject_answered_questions' => $topicsData->sum('answered_questions'),
                'subject_total_marks'       => $topicsData->sum('total_marks'),
                'subject_student_marks'     => $topicsData->sum('student_marks'),
                'average_score'             => $topicsData->sum('total_marks') > 0
                    ? round(($topicsData->sum('student_marks') / $topicsData->sum('total_marks')) * 100, 2)
                    : 0,
            ];
        })->values();

        return response()->json([
            'subjects_data' => $subjectsData
        ]);
    }

    // public function PaperScores(Request $request)
    // {
    //     $userId = $request->user_id;

    //     // $student_attempts = DB::table('student_attempts')
    //     //     ->where('student_attempts.user_id', $userId)
    //     //     ->join('papers', 'student_attempts.paper_id', '=', 'papers.id')
    //     //     ->join('exam_papers', 'student_attempts.exam_id', '=', 'exam_papers.id')
    //     //     ->select(
    //     //         'student_attempts.paper_id',
    //     //         'papers.name as paper_name',
    //     //         'student_attempts.exam_id',
    //     //         'exam_papers.title as exam_name',
    //     //         'student_attempts.score',
    //     //         'student_attempts.max_score',
    //     //         'student_attempts.grading_source',
    //     //         'student_attempts.time_remaining',
    //     //         'student_attempts.started_at',
    //     //         'student_attempts.created_at'
    //     //     )
    //     //     ->get();

    //     // return response()->json($student_attempts);

    //     $student_attempts = DB::table('student_attempts')
    //         ->where('student_attempts.user_id', $userId)
    //         ->join('papers', 'student_attempts.paper_id', '=', 'papers.id')
    //         ->select('student_attempts.paper_id', 'papers.name as paper_name')
    //         ->distinct()
    //         ->get();

    //     return response()->json($student_attempts);
    // }

    public function PaperScores(Request $request)
    {
        $userId = $request->user_id;

        $student_attempts = DB::table('student_attempts')
            ->where('student_attempts.user_id', $userId)
            ->join('papers', 'student_attempts.paper_id', '=', 'papers.id')
            ->select(
                'student_attempts.paper_id',
                'papers.name as paper_name',
                DB::raw('AVG(student_attempts.score) as average_score')
            )
            ->groupBy('student_attempts.paper_id', 'papers.name')
            ->get();

        return response()->json($student_attempts);
    }
}
