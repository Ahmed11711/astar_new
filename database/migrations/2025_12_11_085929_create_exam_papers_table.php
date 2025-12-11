<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
 /**
  * Run the migrations.
  */
 public function up()
 {
  Schema::create('exam_papers', function (Blueprint $table) {
   $table->id();
   $table->foreignId('subject_id')->constrained()->cascadeOnDelete();
   $table->foreignId('grade_id')->constrained()->cascadeOnDelete();
   $table->foreignId('paper_id')->nullable()->constrained()->nullOnDelete();

   $table->string('title');
   $table->string('paper_label')->nullable();
   $table->year('year')->nullable();
   $table->string('month')->nullable();
   $table->boolean('is_active')->default(true);

   $table->integer('total_marks')->nullable();
   $table->integer('duration_minutes')->nullable();

   $table->json('meta')->nullable(); // creationMethod, duration, meta.subject, meta.grade, ...
   $table->timestamps();

   $table->index(['subject_id', 'grade_id']);
  });
 }


 /**
  * Reverse the migrations.
  */
 public function down(): void
 {
  Schema::dropIfExists('exam_papers');
 }
};
