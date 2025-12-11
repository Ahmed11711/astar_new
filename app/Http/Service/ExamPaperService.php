<?php

namespace App\Http\Service;

use App\Models\ExamPaper;
use App\Models\Question;
use App\Models\QuestionOption;
use Illuminate\Support\Facades\DB;

class ExamPaperService
{
 public function createExamPaperWithQuestions($data)
 {
  return DB::transaction(function () use ($data) {

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

   $this->insertQuestions($paper->id, $data['questions']);

   return $paper->load('questions');
  });
 }

 private function insertQuestions($paperId, $questions, $parentId = null)
 {
  foreach ($questions as $q) {

   // 1) create main question
   $question = Question::create([
    'exam_paper_id'        => $paperId,
    'subject_id'           => $q['subject_id'] ?? null,
    'topic_id'             => $q['topic_id'] ?? null,
    'question_type'        => $q['question_type'],
    'question_string'      => $q['question_string'] ?? null,
    'question_number'      => $q['question_number'],
    'question_max_score'   => $q['question_max_score'] ?? null,
    // 'parent_question_id'   => $parentId,
    'marking_scheme'       => $q['marking_scheme'] ?? [],
    'has_options'          => isset($q['options']) ? 1 : 0,
   ]);

   // 2) insert options
   if (!empty($q['options'])) {
    $opts = [];
    foreach ($q['options'] as $o) {
     $opts[] = [
      'question_id' => $question->id,
      'text'        => $o['text'],
      'is_correct'  => $o['is_correct'],
     ];
    }
    QuestionOption::insert($opts);
   }

   // 3) insert sub-questions recursively
   if (!empty($q['sub_questions'])) {
    $this->insertQuestions($paperId, $q['sub_questions'], $question->id);
   }
  }
 }
}
