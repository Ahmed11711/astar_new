<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EducationSeeder extends Seeder
{
 /**
  * Run the database seeds.
  */
 public function run(): void
 {
  $grades = [
   ['name' => 'Grade 1', 'order' => '1'],
   ['name' => 'Grade 2', 'order' => '2'],
   //
  ];

  foreach ($grades as $grade) {
   $gradeId = DB::table('grades')->insertGetId($grade);

   $subjects = [
    ['name' => 'Math', 'grade_id' => $gradeId],
    ['name' => 'Science', 'grade_id' => $gradeId],
   ];

   foreach ($subjects as $subject) {
    $subjectId = DB::table('subjects')->insertGetId($subject);

    $topics = [
     ['name' => 'Topic 1', 'subject_id' => $subjectId],
     ['name' => 'Topic 2', 'subject_id' => $subjectId],
    ];

    foreach ($topics as $topic) {
     $topicId = DB::table('topics')->insertGetId($topic);

     $subtopics = [
      ['name' => 'Subtopic 1', 'topic_id' => $topicId],
      ['name' => 'Subtopic 2', 'topic_id' => $topicId],
     ];

     DB::table('subtopics')->insert($subtopics);
    }
   }
  }
 }
}
