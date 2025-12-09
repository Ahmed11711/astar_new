<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Topic extends BaseModel
{
    protected $booleans = ['is_active'];


    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }
}
