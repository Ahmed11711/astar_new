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
    public function topics()
    {
        return $this->hasMany(Topic::class);
    }

    public function examPapers()
    {
        return $this->hasMany(ExamPaper::class);
    }
    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    public function paper()
    {
        return $this->belongsTo(paper::class, 'subject_id');
    }
}
