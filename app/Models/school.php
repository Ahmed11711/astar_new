<?php

namespace App\Models;

use Illuminate\Support\Str;

class school extends BaseModel
{
    protected $booleans = ['is_active'];
    public function setNameAttribute($value)
    {
        $this->attributes['name'] = $value;
        $this->attributes['slug'] = Str::slug($value);
    }
}
