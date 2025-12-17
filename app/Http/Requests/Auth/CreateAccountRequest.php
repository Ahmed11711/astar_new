<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\BaseRequest\BaseRequest;
use Illuminate\Validation\Rule;

class CreateAccountRequest extends BaseRequest
{
 public function rules(): array
 {
  return [
   'username'    => ['required', 'string', 'max:255'],
   'first_name'  => ['required', 'string', 'max:255'],
   'last_name'   => ['required', 'string', 'max:255'],
   'email'       => ['required', 'email', 'unique:users,email'],
   'phone'       => ['nullable', 'string', 'max:20'],
   'role'        => ['required', Rule::in(['student', 'teacher', 'school', 'data_entry'])],
   'password'    => ['required', 'string', 'min:6'],

   'student_type' => [
    Rule::requiredIf(fn() => $this->role === 'student'),
    Rule::in(['school', 'teacher', 'individual']),
   ],

   'grade_id' => [
    Rule::requiredIf(fn() => $this->role === 'student'),
    'exists:grades,id',
   ],

   'school_id' => [
    Rule::requiredIf(fn() => $this->role === 'student'),
    Rule::exists('users', 'id')->where(function ($query) {
        $query->where('role', '!=', 'student');
    }),
],


   'pakage_id' => [
    Rule::requiredIf(fn() => $this->role === 'student'),
    'exists:packages,id',
   ],


   'subject_ids' => [
    'sometimes',
    'array',
    Rule::requiredIf(fn() => $this->role === 'student'),
   ],
   'subject_ids.*' => ['integer', 'exists:subjects,id'],
  ];
 }

 public function messages(): array
 {
  return [
   'username.required' => 'Username is required.',
   'username.unique'   => 'This username is already taken.',

   'first_name.required' => 'First name is required.',
   'last_name.required'  => 'Last name is required.',

   'email.required' => 'Email is required.',
   'email.email'    => 'Email must be a valid email address.',
   'email.unique'   => 'This email is already registered.',

   'role.required' => 'Role is required.',
   'role.in'       => 'Role must be student, teacher or school.',

   'student_type.required' => 'Student type is required when role is student.',
   'student_type.in'       => 'Student type must be school, teacher or individual.',

   'grade_id.required' => 'Grade is required when role is student.',
   'grade_id.exists'   => 'Selected grade does not exist.',

   'school_id.required' => 'School is required when role is student.',
   'school_id.exists'   => 'Selected school does not exist.',
  ];
 }
}
