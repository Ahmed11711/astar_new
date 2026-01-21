<?php

namespace App\Http\Requests\Teacher;

use App\Http\Requests\BaseRequest\BaseRequest;

class AssignStudentRequest extends BaseRequest
{



    public function rules(): array
    {
        return [
            'email' => 'required|string|email|unique:student_registrations,email',
        ];
    }
}
