<?php

namespace App\Http\Requests\Student\Answer;

use App\Http\Requests\BaseRequest\BaseRequest;
use Illuminate\Foundation\Http\FormRequest;

class SaveAnswerRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'attempt_id' => 'required',
            'is_saved' => 'required|boolean',
            'answers' => 'required|array|min:1',
            'answers.*.question_id'    => 'nullable|integer|exists:questions,id',
            'answers.*.question_index' => 'nullable|integer|min:0',
            'answers.*.response'       => 'nullable|array', // JSON من الفرونت
            'answers.*.is_flagged'     => 'boolean',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $answers = $this->input('answers', []);
            foreach ($answers as $a) {
                if (!isset($a['question_id']) && !isset($a['question_index'])) {
                    $validator->errors()->add('question', 'Either question_id or question_index is required for each answer.');
                }
            }
        });
    }
}
