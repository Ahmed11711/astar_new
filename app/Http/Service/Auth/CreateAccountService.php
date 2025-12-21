<?php

namespace App\Http\Service\Auth;

use App\Models\Packages;
use App\Models\StudentAssignment;
use App\Models\StudentPackage;
use App\Models\StudentSubject;
use App\Models\User;
use App\Models\UserGrade;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class CreateAccountService
{
    protected ?int $packageDuration = null;

    public function execute(array $data): User
    {
        return DB::transaction(fn() => $this->createUserWithRelations($data));
    }

    protected function createUserWithRelations(array $data): User
    {
        $user = User::create([
            'username'     => $data['username'],
            'first_name'   => $data['first_name'],
            'last_name'    => $data['last_name'],
            'email'        => $data['email'],
            'phone'        => $data['phone'] ?? '555',
            'role'         => $data['role'],
            'student_type' => $data['student_type'] ?? null,
            'is_active'    => true,
            'password'     => Hash::make($data['password']),
        ]);

        if ($user->role !== 'student') {
            return $user;
        }

        $this->assignStudentAssignment($user, $data);
        $this->assignGrade($user, $data);
        $this->assignPackage($user, $data);
        $this->assignSubjects($user, $data);

        return $user;
    }

    protected function assignStudentAssignment(User $user, array $data): void
    {
        if (empty($data['school_id']) || empty($data['directorate_affiliation'])) return;

        StudentAssignment::create([
            'student_id'    => $user->id,
            'assigned_type' => $data['directorate_affiliation'],
            'assigned_id'   => $data['school_id'],
        ]);
    }

    protected function assignGrade(User $user, array $data): void
    {
        if (empty($data['grade_id'])) return;

        UserGrade::create([
            'user_id'  => $user->id,
            'grade_id' => $data['grade_id'],
        ]);
    }

    protected function assignPackage(User $user, array $data): void
    {
        if (empty($data['package_id'])) return;

        $this->packageDuration ??= Packages::where('id', $data['package_id'])
            ->value('duration_days');

        StudentPackage::create([
            'student_id' => $user->id,
            'package_id' => $data['package_id'],
            'starts_at'  => now(),
            'ends_at'    => now()->addDays($this->packageDuration ?? 30),
            'active'     => true,
        ]);
    }

    protected function assignSubjects(User $user, array $data): void
    {
        if (empty($data['subject_ids'])) return;

        $now = now();

        StudentSubject::insert(
            collect($data['subject_ids'])
                ->unique()
                ->map(fn($id) => [
                    'student_id' => $user->id,
                    'subject_id' => $id,
                    'created_at' => $now,
                    'updated_at' => $now,
                ])
                ->all()
        );
    }
}
