<?php

namespace App\Http\Requests;

use App\Http\Requests\BaseRequest\BaseRequest;
use Illuminate\Foundation\Http\FormRequest;

class ExamPaperRequest extends BaseRequest
{

 /**
  * Get the validation rules that apply to the request.
  *
  * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
  */
 public function rules(): array
 {
  return [
   'subject_id'        => 'required|exists:subjects,id',
   'grade_id'          => 'required|exists:grades,id',
   'title'             => 'required|string|max:255',
   'paper_label'       => 'nullable|string|max:255',
//    'paper_id'          =>'required|exists:table,column',
   'year'              => 'nullable|integer',
   'month'             => 'nullable|string',
  //  'is_active'         => 'nullable|boolean',
   'total_marks'       => 'nullable|integer',
   'duration_minutes'  => 'nullable|integer',
   'meta'              => 'nullable|array',

   // الأسئلة
   'questions'                 => 'required|array',
   'questions.*.question_type' => 'required|string',
   'questions.*.question_string' => 'nullable|string',
   'questions.*.question_number' => 'required|string',
   'questions.*.question_max_score' => 'nullable|integer',
   'questions.*.parent_question_number' => 'nullable|string',
   'questions.*.topic_id' => 'required|integer|exists:topics,id',
   'questions.*.subtopics_id' => 'required|integer|exists:subtopics,id',

   // الخيارات
   'questions.*.options'                  => 'array',
   'questions.*.options.*.text'           => 'required_with:questions.*.options|string',
   'questions.*.options.*.is_correct'     => 'boolean',

   // marking
   'questions.*.marking_scheme' => 'array',
   'questions.*.marking_scheme.*.answer_raw'   => 'nullable|string',
   'questions.*.marking_scheme.*.grade_type'   => 'nullable|string',
   'questions.*.marking_scheme.*.grade_score'  => 'nullable|integer',

   // sub questions (recursive)
   'questions.*.sub_questions' => 'array',
  ];
 }
}
