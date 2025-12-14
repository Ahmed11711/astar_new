<?php

namespace App\Http\Requests\Admin\ExamPaper;

use App\Http\Requests\BaseRequest\BaseRequest;

class ExamPaperUpdateRequest extends BaseRequest
{
 public function authorize(): bool
 {
  return true;
 }

 public function rules(): array
 {
  return [
   'subject_id' => 'sometimes|required|integer|exists:subjects,id',
   'grade_id' => 'sometimes|required|integer|exists:grades,id',
   'paper_id' => 'nullable|sometimes|integer|exists:papers,id',
   'title' => 'sometimes|required|string|max:255',
   'paper_label' => 'nullable|sometimes|string|max:255',
   'year' => 'nullable|sometimes',
   'month' => 'nullable|sometimes|string|max:255',
   'is_active' => 'sometimes|required|integer',
   'total_marks' => 'nullable|sometimes|integer',
   'duration_minutes' => 'nullable|sometimes|integer',
   'meta' => 'nullable|sometimes|array',
  ];
 }
}
