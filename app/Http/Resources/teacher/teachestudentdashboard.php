<?php

namespace App\Http\Resources\teacher;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class teachestudentdashboard extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'username' => $this->username,
            'email' => $this->email,

            'grades' => $this->grades->map(fn($grade) => [
                'id' => $grade->id,
                'name' => $grade->name,
            ]),

            'subjects' => $this->subjects->map(function ($subject) {
                return [
                    'id' => $subject->id,
                    'name' => $subject->name,

                    'topics' => $subject->topics->map(function ($topic) {
                        $questions = $topic->questions;

                        $answeredQuestions = $questions->filter(
                            fn($q) => $q->answers->isNotEmpty()
                        );

                        $scores = $answeredQuestions
                            ->pluck('answers')
                            ->flatten()
                            ->pluck('attempt.score');

                        return [
                            'id' => $topic->id,
                            'name' => $topic->name,

                            'questions_count' => $questions->count(),
                            'answered_questions' => $answeredQuestions->count(),

                            'total_score' => $scores->sum(),
                            'average_score' => $scores->count()
                                ? round($scores->avg(), 2)
                                : 0,
                        ];
                    }),
                ];
            }),
        ];
    }
}
