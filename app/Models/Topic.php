<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Topic extends BaseModel
{
    protected $booleans = ['is_active'];


    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }
    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    public function subTopics()
    {
        return $this->hasMany(Subtopic::class, 'topic_id');
    }

    public function subTopic()
    {
        return $this->hasMany(Subtopic::class, 'topic_id');
    }
}
