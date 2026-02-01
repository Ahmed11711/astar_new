<?php

namespace App\Http\Service\HelperService;

use App\Models\User;

class UserRoleService
{
    public function getTeachersAndSchools()
    {
        /** =========================
         *  1️⃣ المدرسين الأفراد
         * ========================= */
        $individualTeachers = User::query()
            ->where('role', 'teacher')
            ->where('student_type', 'individual')
            ->select('id', 'username', 'email')
            ->get();

        /** =========================
         *  2️⃣ المدارس + المدرسين
         * ========================= */
        $schools = User::query()
            ->where('role', 'school')
            ->select('id', 'username', 'email')
            ->with([
                'teachers:id,username,email'
            ])
            ->get()
            ->map(function ($school) {
                return [
                    'school'   => $school->only(['id', 'username', 'email']),
                    'teachers' => $school->teachers,
                ];
            });

        return [
            'teachers' => $individualTeachers,
            'schools'  => $schools,
        ];
    }
}
