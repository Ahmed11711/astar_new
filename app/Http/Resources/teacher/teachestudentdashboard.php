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

                // 1️⃣ جمع بيانات المواضيع
                $topics = $subject->topics->map(function ($topic) {
                    $questions = $topic->questions;

                    $answeredQuestions = $questions->filter(
                        fn($q) => $q->answers->isNotEmpty()
                    );

                    $scores = $answeredQuestions
                        ->pluck('answers')     // جمع كل الإجابات
                        ->flatten()
                        ->pluck('attempt.score') // درجات كل محاولة
                        ->filter(); // safety

                    return [
                        'id'                => $topic->id,
                        'name'              => $topic->name,
                        'questions_count'   => $questions->count(),
                        'answered_questions' => $answeredQuestions->count(),
                        'total_score'       => $scores->sum(),
                        'average_score'     => $scores->count() ? round($scores->avg(), 2) : 0,
                    ];
                });

                // 2️⃣ حساب score كامل للـ subject
                $subjectScore = $topics->pluck('total_score')->sum();

                return [
                    'id'     => $subject->id,
                    'name'   => $subject->name,
                    'score'  => $subjectScore, // المجموع لكل الـ topics
                    'topics' => $topics,
                ];
            }),
        ];
    }
}
