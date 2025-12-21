<?php

namespace App\Http\Controllers\Student\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Topic;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $userId = $request->user_id;
        $subjectIds = $request->student_subject_ids;

        $topics = Topic::whereIn('subject_id', $subjectIds)
            ->with(['questions.answers' => function ($query) use ($userId) {
                $query->where('user_id', $userId);
            }])
            ->get();

        $result = $topics->map(function ($topic) {

            $totalMarks = $topic->questions->sum('question_max_score');

            $studentMarks = $topic->questions->sum(function ($question) {
                return $question->answers->sum('mark_score');
            });

            $answeredQuestions = $topic->questions->filter(function ($question) {
                return $question->answers->isNotEmpty();
            })->count();

            return [
                'topic_name' => $topic->name,
                'total_marks' => $totalMarks,
                'student_marks' => $studentMarks,
                'answered_questions' => $answeredQuestions,
            ];
        });

        return response()->json($result);
    }
}
