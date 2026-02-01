<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserPackageFeature extends Model
{
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function package()
    {
        return $this->belongsTo(Packages::class);
    }

    public function feature()
    {
        return $this->belongsTo(FeaturePackage::class, 'package_feature_id');
    }
}
