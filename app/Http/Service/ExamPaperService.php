<?php

namespace App\Http\Service;

use App\Models\ExamPaper;
use App\Models\Question;
use App\Models\QuestionOption;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ExamPaperService
{
    private $questionsMap = []; 

    public function createExamPaperWithQuestions($data)
    {
        return DB::transaction(function () use ($data) {

            // =========================
            // Create Exam Paper
            // =========================
            $paper = ExamPaper::create([
                'subject_id'       => $data['subject_id'],
                'grade_id'         => $data['grade_id'],
                'paper_id'         => $data['paper_id'] ?? null,
                'title'            => $data['title'],
                'paper_label'      => $data['paper_label'] ?? null,
                'year'             => $data['year'] ?? null,
                'month'            => $data['month'] ?? null,
                'is_active'        => $data['is_active'] ?? true,
                'total_marks'      => $data['total_marks'] ?? null,
                'duration_minutes' => $data['duration_minutes'] ?? null,
                'meta'             => $data['meta'] ?? [],
            ]);

            // =========================
            // Insert Questions + Media
            // =========================
            $this->insertQuestions($paper->id, $data['questions'], null, $paper->subject_id, $data['topic'] ?? null);

            return $paper->load('questions');
        });
    }

    private function insertQuestions($paperId, $questions, $parentId = null, $subjectId = null, $topicId = null)
    {
        foreach ($questions as $q) {


            if (!empty($q['parent_question_number']) && isset($this->questionsMap[$q['parent_question_number']])) {
                $parentId = $this->questionsMap[$q['parent_question_number']];
            } else {
                $parentId = null;
            }

            // =========================
            //  Create Question
            // =========================
            $question = Question::create([
                'exam_paper_id'      => $paperId,
                'subject_id'         => $subjectId,
                'topic_id'           => $q['topic_id'] ?? null,
                'subtopics_id'       => $q['subtopics_id'] ?? null,
                'question_type'      => $q['question_type'],
                'question_string'    => $q['question_string'] ?? null,
                'question_number'    => $q['question_number'],
                'question_max_score' => $q['question_max_score'] ?? null,
'marking_scheme' => array_key_exists('marking_scheme', $q)
    ? $q['marking_scheme']
    : [],
                'has_options'        => !empty($q['options']),
                'parent_id'          => $parentId,
            ]);

            $this->questionsMap[$q['question_number']] = $question->id;

            // =========================
            // Insert Options
            // =========================
            if (!empty($q['options'])) {
                $opts = array_map(fn($o) => [
                    'question_id' => $question->id,
                    'text'        => $o['text'],
                    'is_correct'  => $o['is_correct'],
                ], $q['options']);
                QuestionOption::insert($opts);
            }

            // =========================
            // Insert Images (FormData Upload)
            // =========================


if (!empty($q['new_images'])) {
    foreach ($q['new_images'] as $file) {

        $path = $file->store('questions', 'public'); 
        // $path = questions/filename.png

        $fullPath = url('storage/app/public/' . $path);

        DB::table('question_images')->insert([
            'question_id' => $question->id,
            'path'        => $fullPath,
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);

        Log::info("Uploaded image for question {$question->id}: $fullPath");
    }
}


            // =========================
            // Insert Audios (FormData Upload)
            // =========================
if (!empty($q['new_audios'])) {
    foreach ($q['new_audios'] as $file) {

        $path = $file->store('questions', 'public');
        // questions/filename.mp3

        $fullPath = url('storage/app/public/' . $path);

        DB::table('question_audios')->insert([
            'question_id' => $question->id,
            'path'        => $fullPath, // ✅ المسار الكامل
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);

        Log::info("Uploaded audio for question {$question->id}: $fullPath");
    }
}



            // =========================
            // Recursive Sub Questions
            // =========================
            if (!empty($q['sub_questions'])) {
                $this->insertQuestions($paperId, $q['sub_questions'], $question->id, $subjectId, $topicId);
            }
        }
    }
}
