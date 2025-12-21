<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\BaseRequest\BaseRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use App\Models\User;
use App\Models\Packages;

class CreateAccountRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            // ======================
            // Basic User Info
            // ======================
            'username'   => ['required', 'string', 'max:255'],
            'first_name' => ['required', 'string', 'max:255'],
            'last_name'  => ['required', 'string', 'max:255'],
            'email'      => ['required', 'email', 'unique:users,email'],
            'phone'      => ['nullable', 'string', 'max:20'],

            // ======================
            // Role
            // ======================
            'role' => [
                'required',
                Rule::in(['student', 'teacher', 'school', 'data_entry']),
            ],

            // ======================
            // Security
            // ======================
            'password' => [
                'required',
                Password::min(8),
            ],

            // ======================
            // Student Specific
            // ======================
            'student_type' => [
                Rule::requiredIf(fn() => $this->role === 'student'),
                Rule::in(['school', 'teacher', 'individual']),
            ],

            'grade_id' => [
                Rule::requiredIf(fn() => $this->role === 'student'),
                'integer',
                'exists:grades,id',
            ],

            // ======================
            // Assignment (School / Teacher)
            // ======================
            'directorate_affiliation' => [
                Rule::requiredIf(fn() => $this->role === 'student' && in_array($this->student_type, ['school', 'teacher'])),
                Rule::in(['school', 'teacher']),
            ],

            'school_id' => [
                Rule::requiredIf(fn() => $this->role === 'student' && in_array($this->student_type, ['school', 'teacher'])),
                'integer',
            ],

            // ======================
            // Package
            // ======================
            'package_id' => [
                Rule::requiredIf(fn() => $this->role === 'student'),
                'integer',
            ],

            // ======================
            // Subjects
            // ======================
            'subject_ids' => [
                Rule::requiredIf(fn() => $this->role === 'student' && $this->student_type !== 'individual'),
                'array',
            ],

            'subject_ids.*' => [
                'integer',
                'exists:subjects,id',
            ],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {

            if ($this->role !== 'student') return;

            // ======================
            // Validate Assigned Account
            // ======================
            if (in_array($this->student_type, ['school', 'teacher'])) {
                $exists = User::where('id', $this->school_id)
                    ->where('role', $this->directorate_affiliation)
                    ->exists();

                if (!$exists) {
                    $validator->errors()->add(
                        'school_id',
                        'Assigned account does not match the selected type.'
                    );
                }
            }

            // ======================
            // Validate Package Ownership
            // ======================
            $validPackage = Packages::where('id', $this->package_id)
                ->where(function ($q) {
                    $q->where('assign_type', 'system')
                        ->orWhere(function ($q) {
                            $q->where('assign_type', $this->directorate_affiliation)
                                ->where('assignable_id', $this->school_id);
                        });
                })
                ->exists();

            if (!$validPackage) {
                $validator->errors()->add(
                    'package_id',
                    'Selected package is not available for this account.'
                );
            }
        });
    }

    public function messages(): array
    {
        return [
            'username.required' => 'Username is required.',
            'username.unique'   => 'This username is already taken.',

            'first_name.required' => 'First name is required.',
            'last_name.required'  => 'Last name is required.',

            'email.required' => 'Email is required.',
            'email.email'    => 'Email must be valid.',
            'email.unique'   => 'Email is already registered.',

            'role.required' => 'Role is required.',
            'role.in'       => 'Invalid role selected.',

            'password.required' => 'Password is required.',
            'password.min'      => 'Password must be at least 8 characters.',

            'student_type.required' => 'Student type is required for students.',
            'student_type.in'       => 'Invalid student type.',

            'grade_id.required' => 'Grade is required for students.',
            'grade_id.exists'   => 'Selected grade does not exist.',

            'directorate_affiliation.required' => 'Directorate affiliation is required.',
            'directorate_affiliation.in'       => 'Directorate affiliation must be school or teacher.',

            'school_id.required' => 'Assigned account is required.',
            'school_id.integer'  => 'Assigned account must be valid.',

            'package_id.required' => 'Package is required.',
            'package_id.integer'  => 'Package must be valid.',

            'subject_ids.required' => 'Subjects are required.',
            'subject_ids.array'    => 'Subjects must be an array.',
        ];
    }
}
