<?php

namespace App\Http\Controllers\Student\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use App\Models\SubTopic;
use App\Models\Topic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;



class DashboardController extends Controller
{

    public function index(Request $request)
    {
        $userId = $request->user_id;
        $subjectIds = $request->student_subject_ids;

        $subjects = Subject::whereIn('id', $subjectIds)
            ->with([
                'topics.questions.answers' => function ($q) use ($userId) {
                    $q->where('user_id', $userId);
                },
                'topics.subTopics.questions.answers' => function ($q) use ($userId) {
                    $q->where('user_id', $userId);
                },
            ])
            ->get();

        $result = $subjects->map(function ($subject) {

            $topics = $subject->topics->map(function ($topic) {

                $topicTotalMarks = $topic->questions->sum('question_max_score');
                $topicStudentMarks = $topic->questions->sum(
                    fn($q) => $q->answers->sum('mark_score')
                );
                $topicAnsweredQuestions = $topic->questions
                    ->where(fn($q) => $q->answers->isNotEmpty())
                    ->count();

                $subTopics = $topic->subTopics->map(function ($sub) {

                    $subTotalMarks = $sub->questions->sum('question_max_score');
                    $subStudentMarks = $sub->questions->sum(
                        fn($q) => $q->answers->sum('mark_score')
                    );
                    $subAnsweredQuestions = $sub->questions
                        ->where(fn($q) => $q->answers->isNotEmpty())
                        ->count();

                    return [
                        'subtopic_name'       => $sub->name,
                        'total_marks'         => $subTotalMarks,
                        'student_marks'       => $subStudentMarks,
                        'answered_questions'  => $subAnsweredQuestions,
                    ];
                });

                return [
                    'topic_name'          => $topic->name,
                    'total_marks'         => $topicTotalMarks,
                    'student_marks'       => $topicStudentMarks,
                    'answered_questions'  => $topicAnsweredQuestions,
                    'subtopics'           => $subTopics,
                ];
            });

            return [
                'subject_name' => $subject->name,
                'subject_id' => $subject->id,
                'topics'       => $topics,
            ];
        });

        return response()->json($result);
    }




    public function topicProgressPerDay(Request $request)
    {
        $userId = $request->user_id;

        $from = $request->from ?? now()->subWeek()->startOfDay();
        $to   = $request->to   ?? now()->endOfDay();

        $data = DB::table('topics')
            ->join('subtopics', 'subtopics.topic_id', '=', 'topics.id')
            ->join('questions', 'questions.subtopics_id', '=', 'subtopics.id')
            ->leftJoin('answers', function ($join) use ($userId, $from, $to) {
                $join->on('answers.question_id', '=', 'questions.id')
                    ->where('answers.user_id', $userId)
                    ->whereBetween('answers.created_at', [$from, $to]);
            })
            ->groupBy(
                'topics.id',
                'topics.name',
                'subtopics.id',
                'subtopics.name',
                DB::raw('DATE(answers.created_at)')
            )
            ->select(
                'topics.id as topic_id',
                'topics.name as topic_name',
                'subtopics.id as subtopic_id',
                'subtopics.name as subtopic_name',

                DB::raw('DATE(answers.created_at) as day'),
                DB::raw('COUNT(DISTINCT answers.question_id) as answered_questions')
            )
            ->orderBy('day')
            ->get();

        return response()->json([
            'from' => $from,
            'to'   => $to,
            'data' => $data,
        ]);
    }
}
