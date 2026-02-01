<?php

namespace App\Http\Controllers\Student\Package;

use App\Http\Controllers\Controller;
use App\Http\Requests\Student\Package\PackageUpgradeRequest;
use App\Http\Resources\Student\PackageResource;
use App\Http\Service\Payment\KashierPaymentService;
use App\Http\Service\Student\StudentPackageFeatureService;
use App\Models\Packages;
use App\Models\StudentAssignment;
use App\Models\StudentPackage;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PakageController extends Controller
{
    use ApiResponseTrait;

    protected $featureService;

    public function __construct(StudentPackageFeatureService $featureService)
    {
        $this->featureService = $featureService;
    }
    public function getPackageByAccount(Request $request)
    {
        $userId = $request->user_id;

        $userPackageId = StudentPackage::where([
            'student_id' => $userId,
            'active'     => true,
            'status'     => 'paid',
        ])
            ->latest()
            ->value('package_id');

        $assignment = StudentAssignment::where('student_id', $userId)
            ->latest()
            ->first();

        $packages = Packages::query()
            ->with('featuresPackage')
            ->when(
                $assignment,
                fn($q) => $q->where([
                    'assignable_id' => $assignment->assigned_id,
                    'assign_type'   => $assignment->assigned_type,
                ]),
                fn($q) => $q->whereNull('assignable_id')
                    ->where('assign_type', 'system')
            )
            ->get();

        return response()->json([
            'packages'  => PackageResource::collection($packages)
                ->additional(['userPackageId' => $userPackageId]),
            'myPackage' => $userPackageId,
        ]);
    }


    public function upgrade(
        PackageUpgradeRequest $request,
        KashierPaymentService $payment
    ) {
        $data = $request->validated();
        $userId = $request->user_id;
        $email = $request->user_email ?? 'test@gmail.com';

        $package = Packages::findOrFail($data['package_id']);

        if ($package->price <= 0) {
            $hasUsedFree = StudentPackage::where('student_id', $userId)
                ->where('type', 'free')
                ->exists();

            if ($hasUsedFree) {
                return $this->errorResponse(
                    'You have already used the free package.',
                    403
                );
            }
        }

        StudentPackage::where('student_id', $userId)
            ->where('active', true)
            ->update(['active' => false]);

        $studentPackage = StudentPackage::create([
            'student_id'     => $userId,
            'package_id'     => $package->id,
            'price'          => $package->price,
            'starts_at'      => now(),
            'ends_at'        => now()->addDays($package->duration_days ?? 30),
            'status'         => 'pending',
            'active'         => false,
            'type'           => $package->price > 0 ? 'not_free' : 'free',
            'transaction_id' => 'TXN-' . Str::uuid(),
        ]);

        if ($package->price <= 0) {
            return $this->successResponse([
                'payment_required' => false,
                'student_package'  => $studentPackage,
            ], 'Free package activated successfully');
        }

        $paymentUrl = $payment->createSession(
            $studentPackage->price,
            $email,
            $studentPackage->transaction_id
        );

        return $this->successResponse([
            'payment_url'      => $paymentUrl,
        ], 'Payment session created successfully');
    }
}
