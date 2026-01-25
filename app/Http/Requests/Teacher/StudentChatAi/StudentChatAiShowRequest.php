<?php

namespace App\Http\Requests\Teacher\StudentChatAi;

use App\Http\Requests\BaseRequest\BaseRequest;

class StudentChatAiShowRequest extends BaseRequest
{

    public function rules(): array
    {
        return [
            'student_id' => 'required|integer|exists:users,id',
            'chat_id' => 'required|integer|exists:chat_ais,id',
        ];
    }
}
