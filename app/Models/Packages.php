<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Packages extends Model
{
    //

    public function scopeForAccount($query, $assignId = null)
    {
        if ($assignId) {
            return $query->where('assignable_id', $assignId);
        }

        return $query->where('assign_type', 'system');
    }

    public function features()
    {
        return $this->belongsToMany(Feature::class)
            ->withPivot('value')
            ->withTimestamps();
    }
}
