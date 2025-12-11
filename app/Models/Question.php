<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
 protected $casts = [
  'marking_scheme' => 'array',
 ];
}
