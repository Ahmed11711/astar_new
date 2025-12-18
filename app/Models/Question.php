<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    protected $casts = [
        'marking_scheme' => 'array',
    ];

    /**
     * خيارات السؤال (MCQ مثلاً)
     */
    public function options()
    {
        return $this->hasMany(QuestionOption::class, 'question_id');
    }

    /**
     */
    public function audios()
    {
        return $this->hasMany(QuestionAudio::class, 'question_id');
    }

    /**
     */
    public function images()
    {
        return $this->hasMany(QuestionImage::class, 'question_id');
    }
}
