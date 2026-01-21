<?php

namespace App\Http\Resources\teacher;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class teachestudentexams extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'       => $this->id,
            'username' => $this->username,
            'email'    => $this->email,

            // Grades
            'grades' => $this->grades->map(fn($grade) => [
                'id' => $grade->id,
                'name' => $grade->name,
            ]),

            // Subjects
            'subjects' => $this->subjects->map(function ($subject) {

                // -----------------------------
                // 1️⃣ Topics stats لكل subject
                // -----------------------------
                $topicsData = $subject->topics->map(function ($topic) {
                    $questions = $topic->questions;

                    // جميع إجابات الطالب لكل سؤال
                    $answeredQuestions = $questions->map(function ($q) {
                        return $q->answers;
                    })->flatten();

                    $scores = $answeredQuestions->pluck('attempt.score')->filter();

                    return [
                        'topic_id' => $topic->id,
                        'topic_name' => $topic->name,
                        'questions_count' => $questions->count(),
                        'answered_questions' => $answeredQuestions->count(),
                        'total_score' => $scores->sum(),
                        'average_score' => $scores->count() ? round($scores->avg(), 2) : 0,
                    ];
                });

                $subjectScore = $topicsData->pluck('total_score')->sum();

                // -----------------------------
                // 2️⃣ Exam Papers stats لكل subject
                // -----------------------------
                $examsData = $subject->examPapers->map(function ($exam) {

                    $attempts = $exam->studentAttempts;

                    $timeTaken = $attempts->sum('time_taken'); // مجموع الوقت
                    $trials = $attempts->count();             // عدد المحاولات

                    // Topic-level stats داخل كل exam
                    $topicsExam = $exam->topics->map(function ($topic) use ($attempts) {
                        $questions = $topic->questions;

                        $answeredQuestions = $questions->map(function ($q) use ($attempts) {
                            return $attempts->pluck('answers')->flatten()->where('question_id', $q->id);
                        })->flatten();

                        $scores = $answeredQuestions->pluck('score')->filter();

                        return [
                            'topic_id' => $topic->id,
                            'topic_name' => $topic->name,
                            'questions_count' => $questions->count(),
                            'answered_questions' => $answeredQuestions->count(),
                            'total_score' => $scores->sum(),
                            'average_score' => $scores->count() ? round($scores->avg(), 2) : 0,
                        ];
                    });

                    $examTotal = $topicsExam->pluck('total_score')->sum();
                    $examAvg = $topicsExam->pluck('average_score')->avg();

                    return [
                        'exam_id' => $exam->id,
                        'exam_title' => $exam->title,
                        'topics' => $topicsExam,
                        'exam_total_score' => $examTotal,
                        'exam_average' => round($examAvg, 2),
                        'time_taken' => $timeTaken,
                        'trials' => $trials,
                    ];
                });

                return [
                    'id' => $subject->id,
                    'name' => $subject->name,
                    'score' => $subjectScore,
                    'topics' => $topicsData,
                    'exams' => $examsData,
                ];
            }),
        ];
    }
}
