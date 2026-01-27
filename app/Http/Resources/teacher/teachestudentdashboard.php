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

                    return [
                        'id'                 => $topic->id,
                        'name'               => $topic->name,
                        'questions_count'    => $topic->questions_count,
                        'answered_questions' => $topic->answered_questions ?? 0,
                        'total_score'        => $topic->total_score ?? 0,
                        'average_score'      => $topic->answered_questions
                            ? round($topic->total_score / $topic->answered_questions, 2)
                            : 0,
                    ];
                });



                $subjectScore = $topics->pluck('total_score')->sum();

                /* =========================
                 * 2️⃣ Exam Papers الخاصة بالمدرس
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
