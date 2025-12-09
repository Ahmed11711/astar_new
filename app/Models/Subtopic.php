<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subtopic extends BaseModel
{
    protected $booleans = ['is_active'];

    public function topic()
    {
        return $this->belongsTo(Topic::class, 'topic_id');
    }

}