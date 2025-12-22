<?php

namespace App\Http\Controllers\Student\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $userId = $request->user_id;
        $subjectIds = $request->student_subject_ids;

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


        $result = $subjects->map(function ($subject) use ($topicsData) {

            $subjectTopics = $topicsData->where('subject_id', $subject->id)
                ->groupBy('topic_id');

            $topics = $subjectTopics->map(function ($topicGroup) {

                $topic = $topicGroup->first();

                $subtopics = $topicGroup->filter(fn($t) => $t->subtopic_id !== null)->map(function ($sub) {
                    return [
                        'subtopic_name'      => $sub->subtopic_name,
                        'total_marks'        => (int)$sub->total_marks,
                        'student_marks'      => (int)$sub->student_marks,
                        'answered_questions' => (int)$sub->answered_questions,
                        'total_questions'    => (int)$sub->total_questions,
                    ];
                })->values();

                return [
                    'topic_name'         => $topic->topic_name,
                    'total_marks'        => (int)$topic->total_marks,
                    'student_marks'      => (int)$topic->student_marks,
                    'answered_questions' => (int)$topic->answered_questions,
                    'total_questions'    => (int)$topic->total_questions,
                    'subtopics'          => $subtopics,
                ];
            })->values();

            $subjectTotalQuestions = $topics->sum('total_questions');
            $subjectAnsweredQuestions = $topics->sum('answered_questions');

            return [
                'subject_name'              => $subject->name,
                'subject_id'                => $subject->id,
                'topics'                    => $topics,
                'subject_total_questions'   => $subjectTotalQuestions,
                'subject_answered_questions' => $subjectAnsweredQuestions,
            ];
        });

        return response()->json($result);
    }
}
