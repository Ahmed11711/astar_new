<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentAssignment extends Model
{

    public function teacher()
    {
        return $this->belongsTo(User::class, 'student_id');
    }
}
