<?php

namespace App\Http\Requests\Teacher;

use App\Http\Requests\BaseRequest\BaseRequest;
use Illuminate\Foundation\Http\FormRequest;

class ShowStudentRequest extends BaseRequest
{


    public function rules(): array
    {
        return [
            'user_id' => 'required|exists:users,id'
        ];
    }
}
