<?php

namespace App\Http\Requests\Teacher;

use App\Http\Requests\BaseRequest\BaseRequest;
use Illuminate\Foundation\Http\FormRequest;

class StudentQuizeRequest extends BaseRequest
{


    public function rules(): array
    {
        return [
            'exam_id' => 'required|exists:exam_papers,id',
            'student_id' => 'required|exists:users,id',
        ];
    }
}
