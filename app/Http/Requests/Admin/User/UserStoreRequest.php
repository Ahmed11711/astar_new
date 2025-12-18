<?php

namespace App\Http\Requests\Admin\User;

use App\Http\Requests\BaseRequest\BaseRequest;
use Illuminate\Support\Facades\Hash;

class UserStoreRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'username' => 'required|string|max:255',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|max:255',
            'phone' => 'nullable|string|max:255',
            'is_email_verified' => 'required|integer',
            'student_type' => 'required|in:individual,school,teacher',
            'role' => 'required|in:admin,school,teacher,student,data_entry',
            'is_active' => 'required|integer',
        ];
    }
}
