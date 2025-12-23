<?php

namespace App\Http\Controllers\Student\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\CarbonPeriod;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $userId = $request->user_id;
        $subjectIds = $request->student_subject_ids;

        $from = $request->from ?? now()->subWeek()->startOfDay();
        $to   = $request->to ?? now()->endOfDay();
        $period = CarbonPeriod::create($from->format('Y-m-d'), $to->format('Y-m-d'));

        $subjects = DB::table('subjects')
            ->whereIn('id', $subjectIds)
            ->get();

        $topicsData = DB::table('topics')
            ->leftJoin('questions as topic_questions', 'topic_questions.topic_id', '=', 'topics.id')
            ->leftJoin('subtopics', 'subtopics.topic_id', '=', 'topics.id')
            ->leftJoin('questions as sub_questions', 'sub_questions.subtopics_id', '=', 'subtopics.id')
            ->leftJoin('answers as topic_answers', function ($join) use ($userId) {
                $join->on('topic_answers.question_id', '=', 'topic_questions.id')
                    ->where('topic_answers.user_id', $userId);
            })
            ->leftJoin('answers as sub_answers', function ($join) use ($userId) {
                $join->on('sub_answers.question_id', '=', 'sub_questions.id')
                    ->where('sub_answers.user_id', $userId);
            })
            ->whereIn('topics.subject_id', $subjectIds)
            ->select(
                'topics.id as topic_id',
                'topics.name as topic_name',
                'topics.subject_id',
                'subtopics.id as subtopic_id',
                'subtopics.name as subtopic_name',
                DB::raw('COUNT(DISTINCT topic_questions.id) + COUNT(DISTINCT sub_questions.id) as total_questions'),
                DB::raw('COUNT(DISTINCT topic_answers.id) + COUNT(DISTINCT sub_answers.id) as answered_questions'),
                DB::raw('SUM(COALESCE(topic_questions.question_max_score,0)) + SUM(COALESCE(sub_questions.question_max_score,0)) as total_marks'),
                DB::raw('SUM(COALESCE(topic_answers.mark_score,0)) + SUM(COALESCE(sub_answers.mark_score,0)) as student_marks')
            )
            ->groupBy('topics.id', 'topics.name', 'topics.subject_id', 'subtopics.id', 'subtopics.name')
            ->get();

        $dailyAnswers = DB::table('answers')
            ->join('questions', 'questions.id', '=', 'answers.question_id')
            ->join('subtopics', 'subtopics.id', '=', 'questions.subtopics_id')
            ->where('answers.user_id', $userId)
            ->whereBetween('answers.created_at', [$from, $to])
            ->select(
                'subtopics.id as subtopic_id',
                DB::raw('DATE(answers.created_at) as day'),
                DB::raw('COUNT(*) as answered_count')
            )
            ->groupBy('subtopics.id', DB::raw('DATE(answers.created_at)'))
            ->orderBy('day')
            ->get();

        $result = $subjects->map(function ($subject) use ($topicsData, $dailyAnswers, $period) {

            $subjectTopics = $topicsData->where('subject_id', $subject->id)
                ->groupBy('topic_id');

            $topics = $subjectTopics->map(function ($topicGroup) use ($dailyAnswers, $period) {

                $topic = $topicGroup->first();

                $subtopics = $topicGroup->filter(fn($t) => $t->subtopic_id !== null)->map(function ($sub) use ($dailyAnswers, $period) {

                    $cumulativeAnswered = 0;
                    $dailyData = [];

                    foreach ($period as $date) {
                        $day = $date->format('Y-m-d');
                        $answeredToday = $dailyAnswers
                            ->where('subtopic_id', $sub->subtopic_id)
                            ->where('day', $day)
                            ->sum('answered_count');

                        $cumulativeAnswered += $answeredToday;
                        $remaining = max($sub->total_questions - $cumulativeAnswered, 0);

                        $dailyData[] = [
                            'day' => $day,
                            'answered' => (int)$answeredToday,
                            'remaining' => (int)$remaining,
                        ];
                    }

                    return [
                        'subtopic_name' => $sub->subtopic_name,
                        'total_marks' => (int)$sub->total_marks,
                        'student_marks' => (int)$sub->student_marks,
                        'answered_questions' => (int)$sub->answered_questions,
                        'total_questions' => (int)$sub->total_questions,
                        'daily' => $dailyData,
                    ];
                })->values();

                $topicTotalMarks = (int)$topic->total_marks + $subtopics->sum('total_marks');
                $topicStudentMarks = (int)$topic->student_marks + $subtopics->sum('student_marks');

                return [
                    'topic_name' => $topic->topic_name,
                    'total_marks' => $topicTotalMarks,
                    'student_marks' => $topicStudentMarks,
                    'answered_questions' => (int)$topic->answered_questions,
                    'total_questions' => (int)$topic->total_questions,
                    'subtopics' => $subtopics,
                ];
            })->values();

            $subjectTotalMarks = $topics->sum('total_marks');
            $subjectStudentMarks = $topics->sum('student_marks');
            $subjectTotalQuestions = $topics->sum('total_questions');
            $subjectAnsweredQuestions = $topics->sum('answered_questions');

            return [
                'subject_name' => $subject->name,
                'subject_id' => $subject->id,
                'topics' => $topics,
                'subject_total_questions' => $subjectTotalQuestions,
                'subject_answered_questions' => $subjectAnsweredQuestions,
                'subject_total_marks' => $subjectTotalMarks,
                'subject_student_marks' => $subjectStudentMarks,
            ];
        });

        return response()->json($result);
    }
}
