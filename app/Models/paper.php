<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class paper extends Model
{
    protected $guarded = [];

    public function examPaper()
    {
        return $this->hasMany(ExamPaper::class, 'paper_id', 'id');
    }

    public function grade()
    {
        return $this->belongsTo(grade::class, 'grade_id', 'id');
    }
    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_id', 'id');
    }
}
