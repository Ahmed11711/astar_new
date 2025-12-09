<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    protected $booleans = ['is_active'];
    public function grades()
    {
        return $this->belongsTo(grade::class, 'grades_id');
    }
}
