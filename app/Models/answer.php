<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class answer extends Model
{
    public function attempt()
    {
        return $this->belongsTo(StudentAttamp::class, 'attempt_id');
    }
}
