<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExamPaper extends Model
{
 protected $guarded = [];

 protected $casts = [
  'is_active' => 'boolean',
  'meta'      => 'array',
 ];

 public function questions()
 {
  return $this->hasMany(Question::class, 'exam_paper_id');
 }

 public function subject()
 {
  return $this->belongsTo(Subject::class, 'subject_id');
 }


 public function grade()
 {
  return $this->belongsTo(grade::class, 'grade_id');
 }


 public function paper()
 {
  return $this->belongsTo(paper::class, 'paper_id');
 }
}
