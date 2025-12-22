<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class chatAi extends Model
{

    protected $fillable = ['user_id', 'role', 'title', 'content', 'parent_id', 'rating'];

    // العلاقة مع الردود
    public function replies()
    {
        return $this->hasMany(chatAi::class, 'parent_id');
    }

    // العلاقة للاب (optional)
    public function parent()
    {
        return $this->belongsTo(chatAi::class, 'parent_id');
    }
}
