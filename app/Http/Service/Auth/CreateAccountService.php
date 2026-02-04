<?php

namespace App\Http\Service\Auth;

use App\Http\Service\Student\StudentPackageFeatureService;
use App\Models\Packages;
use App\Models\StudentAssignment;
use App\Models\StudentPackage;
use App\Models\StudentSubject;
use App\Models\User;
use App\Models\UserGrade;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CreateAccountService
{
    protected $featureService;

    public function __construct(StudentPackageFeatureService $featureService)
    {
        $this->featureService = $featureService;
    }
    protected ?int $packageDuration = null;

    public function execute(array $data): User
    {
        return DB::transaction(fn() => $this->createUserWithRelations($data));
    }

    protected function createUserWithRelations(array $data)
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

        $studentPackage = null;
        $this->assignStudentAssignment($user, $data);
        $this->assignGrade($user, $data);
        $this->assignSubjects($user, $data);

        if ($user->role === 'student') {

            $studentPackage = $this->assignPackage($user, $data);
        }


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
    protected function assignPackage(User $user, array $data)
    {
        if (empty($data['package_id'])) {
            return;
        }

        $package = Packages::select('id', 'price', 'duration_days')
            ->find($data['package_id']);

        if (!$package) {
            return;
        }
        $transactionId = 'TXN-' . Str::uuid();

        // Create features for the user based on the package
        $this->featureService->createFeaturesForUser($user->id, $package);

        return StudentPackage::create([
            'student_id'     => $user->id,
            'package_id'     => $package->id,
            'price'          => $package->price ?? 0,
            'starts_at'      => now(),
            'ends_at'        => now()->addDays($package->duration_days ?? 30),
            'active'         => true,
            'status'         => ($package->price ?? 0) == 0 ? 'paid' : 'pending',
            'type'           => ($package->price ?? 0) > 0 ? 'not_free' : 'free',
            'transaction_id' => $transactionId,
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
