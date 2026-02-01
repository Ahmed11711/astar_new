<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserPackageFeature extends Model
{
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function packageFeature()
    {
        return $this->belongsTo(FeaturePackage::class, 'package_feature_id');
    }

    public function feature()
    {
        return $this->hasOneThrough(
            Feature::class,
            FeaturePackage::class,
            'id',               // FK in FeaturePackage
            'id',               // PK in Feature
            'package_feature_id', // localKey in UserPackageFeature
            'feature_id'        // localKey in FeaturePackage
        );
    }
}
