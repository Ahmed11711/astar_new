<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
}
