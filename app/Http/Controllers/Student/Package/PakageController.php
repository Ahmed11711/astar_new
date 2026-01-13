<?php

namespace App\Http\Controllers\Student\Package;

use App\Http\Controllers\Controller;
use App\Http\Requests\Student\Package\PackageUpgradeRequest;
use App\Http\Resources\Student\PackageResource;
use App\Http\Service\Payment\KashierPaymentService;
use App\Models\Packages;
use App\Models\StudentAssignment;
use App\Models\StudentPackage;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PakageController extends Controller
{
    use ApiResponseTrait;
    public function getPackageByAccount(Request $request)
    {
        $userId = $request->user_id;

        $myAssignment = StudentAssignment::where('student_id', $userId)
            ->latest()
            ->first();

        if (!$myAssignment) {
            return response()->json([
                'packages' => [],
                'myPackage' => null
            ]);
        }

        $userPackageId = StudentPackage::where('student_id', $userId)
            ->latest()
            ->value('package_id');

        $packages = Packages::where('assignable_id', $myAssignment->assigned_id)
            ->where('assign_type', $myAssignment->assigned_type)
            ->with('featuresPackage')
            ->get()
            ->map(function ($package) use ($userPackageId) {
                $package->is_user_package = $package->id == $userPackageId;
                return $package;
            });

        return response()->json([
            'packages' => PackageResource::collection($packages),
            'myPackage' => $userPackageId
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
