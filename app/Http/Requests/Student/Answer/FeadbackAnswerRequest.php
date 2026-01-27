<?php

namespace App\Http\Requests\Student\Answer;

use App\Http\Requests\BaseRequest\BaseRequest;

class FeadbackAnswerRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'answer_id' => ['required', 'integer', 'exists:answers,id'],
            'feadback' => ['required', 'string', 'max:1000'],

        ];
    }
}
