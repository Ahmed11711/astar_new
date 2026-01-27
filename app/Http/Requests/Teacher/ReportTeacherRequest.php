<?php

namespace App\Http\Requests\Teacher;

use App\Http\Requests\BaseRequest\BaseRequest;

class ReportTeacherRequest extends BaseRequest
{

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
        ];
    }
}
