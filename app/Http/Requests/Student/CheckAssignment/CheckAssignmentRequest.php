<?php

namespace App\Http\Requests\Student\CheckAssignment;

use App\Http\Requests\BaseRequest\BaseRequest;

class CheckAssignmentRequest extends BaseRequest
{

    public function rules(): array
    {
        return [
            'email' => 'required|email',
            'assignment_id' => 'required|integer|exists:users,id',
        ];
    }
}
