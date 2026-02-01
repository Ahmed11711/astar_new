<?php

namespace App\Http\Service\Student;

use App\Models\User;
use App\Models\UserPackageFeature;
use Illuminate\Support\Facades\DB;
use App\Models\Packages;


class StudentPackageFeatureService
{
    public function createFeaturesForUser(int $userId, Packages $package)
    {
        $features = $package->featuresPackage; // assuming relation featuresPackage

        DB::transaction(function () use ($features, $userId, $package) {
            foreach ($features as $feature) {
                UserPackageFeature::create([
                    'user_id'             => $userId,
                    'package_id'          => $package->id,
                    'package_feature_id'  => $feature->id,
                    'total_count'         => $feature->value,
                    'remaining_count'     => $feature->value,
                ]);
            }
        });

        return true;
    }
}
