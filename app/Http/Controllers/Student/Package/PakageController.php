<?php

namespace App\Http\Controllers\Student\Package;

use App\Http\Controllers\Controller;
use App\Http\Requests\Student\Package\PackageUpgradeRequest;
use App\Http\Resources\Student\PackageResource;
use App\Models\Packages;
use App\Models\StudentAssignment;
use App\Models\StudentPackage;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

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

    public function upgrade(PackageUpgradeRequest $request)
    {
        $userId = $request->user_id;
        $data = $request->validated();

        $data['user_id'] = $userId;
        $data['starts_at'] = now();

        $studentPackage = StudentPackage::create($data);

        return $this->successResponse($studentPackage);

        return response()->json([
            'status' => 'success',
            'message' => 'Package upgraded successfully',
            'data' => $studentPackage
        ]);
    }
}
