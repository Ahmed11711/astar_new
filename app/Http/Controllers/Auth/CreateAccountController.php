<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\CreateAccountRequest;
use App\Models\User;
use App\Models\StudentAssignment;
use App\Models\UserGrade;
use App\Traits\ApiResponseTrait;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class CreateAccountController extends Controller
{
    use ApiResponseTrait;
    public function createAccount(CreateAccountRequest $request)
    {
        return DB::transaction(function () use ($request) {
            $user = User::create($request->only([
                'username',
                'first_name',
                'last_name',
                'email',
                'role',
                'student_type',
                'phone'

            ]) + ['is_active' => true, 'password' => Hash::make($request->password ?? '123456')]);

            if ($user->role === 'student') {
                $assignedId = $request->school_id ?? $request->teacher_id;
                if ($assignedId) {
                    StudentAssignment::create([
                        'student_id'    => $user->id,
                        'assigned_type' => $request->school_id ? 'school' : 'teacher',
                        'assigned_id'   => $assignedId,
                    ]);
                }

                if ($request->filled('grade_id')) {
                    UserGrade::create([
                        'user_id'  => $user->id,
                        'grade_id' => $request->grade_id,
                    ]);
                }
            }

            return $this->successResponse($user, 'Account created successfully');
        });
    }
}
