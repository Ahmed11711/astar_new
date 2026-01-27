<?php

namespace App\Http\Resources\teacher;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class teachestudentdashboard extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'       => $this->id,
            'username' => $this->username,
            'email'    => $this->email,

            'grades' => $this->grades->map(fn($grade) => [
                'id'   => $grade->id,
                'name' => $grade->name,
            ]),

            'subjects' => $this->subjects->map(function ($subject) {

                /* =========================
                 * 1️⃣ Topics Stats  
                 * ========================= */
                $topics = $subject->topics->map(function ($topic) {

                    $questions = $topic->questions;

                    // ✅ إجابات الطالب فقط
                    $studentAnswers = $questions
                        ->pluck('answers')
                        ->flatten()
                        ->where('user_id', $this->id);

                    // ✅ IDs الأسئلة اللي الطالب جاوبها فعليًا
                    $answeredQuestionIds = $studentAnswers
                        ->pluck('question_id')
                        ->unique();

                    // ✅ عدد الأسئلة المتجاوبة للطالب فقط
                    $answeredQuestionsCount = $answeredQuestionIds->count();

                    // ✅ السكورات
                    $scores = $studentAnswers
                        ->pluck('attempt.score')
                        ->filter();

                    return [
                        'id'                 => $topic->id,
                        'name'               => $topic->name,
                        'questions_count'    => $questions->count(),
                        'answered_questions' => $answeredQuestionsCount,
                        'total_score'        => $scores->sum(),
                        'average_score'      => $scores->count()
                            ? round($scores->avg(), 2)
                            : 0,
                    ];
                });





                $subjectScore = $topics->pluck('total_score')->sum();

                /* =========================
                 
                 * ========================= */
                $examPapers = $subject->examPapers->map(function ($exam) {

                    $attempts = $exam->studentAttempts;

                    return [
                        'id'          => $exam->id,
                        'title'       => $exam->title,
                        'trials'      => $attempts->count(),
                        'total_score' => $attempts->sum('score'),
                    ];
                });

                return [
                    'id'          => $subject->id,
                    'name'        => $subject->name,
                    'score'       => $subjectScore,
                    'topics'      => $topics,
                    'exam_papers' => $examPapers,
                ];
            }),
        ];
    }
}
