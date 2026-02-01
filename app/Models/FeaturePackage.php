<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;


class FeaturePackage extends Model
{
    //

    public function package()
    {
        return $this->belongsTo(Packages::class, 'package_id');
    }


    public function feature()
    {
        return $this->belongsTo(Feature::class, 'feature_id');
    }

    protected static function booted()
    {
        static::creating(function ($model) {
            if ($model->feature) {
                // توليد key_feature من اسم الـ Feature
                $model->key_feature = Str::snake($model->feature->key);
            }
        });
    }
}
