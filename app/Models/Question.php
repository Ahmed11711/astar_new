<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    protected $casts = [
        'marking_scheme' => 'array',
    ];

    /**
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

    public function lastAttempt()
    {
        return $this->hasOne(StudentAttamp::class, 'question_id')
            ->latest('attempt_id');
    }

    public function asnwer()
    {
        return $this->hasOne(answer::class, 'question_id');
    }
    // Question.php
    public function answers()
    {
        return $this->hasMany(answer::class, 'question_id');
    }

    public function lastAnswer()
    {
        return $this->hasOne(answer::class, 'question_id')
            ->latest('created_at'); // آخر إجابة
    }
    public function getImagesPathAttribute()
{
    return DB::table('question_images')
        ->where('question_id', $this->id)
        ->get()
        ->map(function ($img) {
            return asset('storage/'.$img->path);
        });
}

}
