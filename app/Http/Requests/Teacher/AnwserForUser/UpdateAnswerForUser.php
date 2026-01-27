<?php

namespace App\Http\Requests\Teacher\AnwserForUser;

use App\Http\Requests\BaseRequest\BaseRequest;
use Illuminate\Foundation\Http\FormRequest;

class UpdateAnswerForUser extends BaseRequest
{
    public function rules(): array
    {
        return [
            'answer_id'       => 'required|integer|exists:answers,id',
            'is_correct'      => 'required|boolean',
            'mark_score'   => 'required|numeric|min:0',
            'teacher_feedback' => 'required|string',
        ];
    }
}
