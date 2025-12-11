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

   // نمرر subject_id و topic للكل
   $this->insertQuestions(
    $paper->id,
    $data['questions'],
    null,
    $paper->subject_id,
    $data['topic'] ?? null
   );

   return $paper->load('questions');
  });
 }

 private function insertQuestions($paperId, $questions, $parentId = null, $subjectId = null, $topicId = null)
 {
  foreach ($questions as $q) {

   $question = Question::create([
    'exam_paper_id'      => $paperId,
    'subject_id'         => $subjectId,      // ثابت من الـ paper
    'topic_id'           => $topicId,        // ثابت من الفرونت
    'question_type'      => $q['question_type'],
    'question_string'    => $q['question_string'] ?? null,
    'question_number'    => $q['question_number'],
    'question_max_score' => $q['question_max_score'] ?? null,
    'marking_scheme'     => $q['marking_scheme'] ?? [],
    'has_options'        => !empty($q['options']),
    // 'parent_question_id' => $parentId,
   ]);

   // insert options
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

   // recursive for sub-questions
   if (!empty($q['sub_questions'])) {
    $this->insertQuestions(
     $paperId,
     $q['sub_questions'],
     $question->id,
     $subjectId,
     $topicId
    );
   }
  }
 }
}
