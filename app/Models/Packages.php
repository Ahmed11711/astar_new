<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Packages extends Model
{
    //



    public function features()
    {
        return $this->belongsToMany(Feature::class)
            ->withPivot('value')
            ->withTimestamps();
    }

    public function featuresPackage()
    {
        return $this->hasMany(FeaturePackage::class, 'package_id', 'id')
            ->with('feature');
    }
}
