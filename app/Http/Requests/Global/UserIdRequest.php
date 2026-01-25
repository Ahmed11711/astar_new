<?php

namespace App\Http\Requests\Global;

use App\Http\Requests\BaseRequest\BaseRequest;

class UserIdRequest extends BaseRequest
{


    public function rules(): array
    {
        return [
            'student_id' => 'required|integer|exists:users,id',
        ];
    }
}
