<?php

namespace App\Http\Requests\Admin\StudentRegistrations;

use Illuminate\Validation\Rule;
use App\Http\Requests\BaseRequest\BaseRequest;

class StudentRegistrationsUpdateRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {

        return [
            'email' => [
                'sometimes',
                'required',
                'string',
                'max:255',
                Rule::unique('student_registrations')->ignore($this->route('student_registrations'))
            ],
            'affiliation_type' => [
                'sometimes',
                'required',
                Rule::in(['school', 'teacher']),
            ],
            'user_id' => [
                'sometimes',
                'required',
                'integer',
                'exists:users,id',
            ],
            'is_active' => [
                'sometimes',
                'required',
                'boolean', // استخدم boolean بدل integer لتوضيح القيمة true/false
            ],
        ];
    }
}
