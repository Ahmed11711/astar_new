<?php

namespace App\Http\Requests\Admin\StudentRegistrations;
use App\Http\Requests\BaseRequest\BaseRequest;
class StudentRegistrationsStoreRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => 'required|string|max:255|unique:student_registrations,email',
            'affiliation_type' => 'required|in:school,teacher',
            'user_id' => 'required|integer|exists:users,id',
            'is_active' => 'required|integer',
        ];
    }
}
