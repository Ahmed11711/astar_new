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
}
