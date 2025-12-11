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
  Schema::create('question_marking_schemes', function (Blueprint $table) {
   $table->id();
   $table->foreignId('question_id')->constrained()->cascadeOnDelete();

   $table->longText('answer_raw')->nullable();
   $table->string('grade_type')->nullable();
   $table->integer('grade_score')->nullable();
   $table->longText('question_string')->nullable();
   $table->json('options')->nullable();
   $table->integer('order')->default(0);
   $table->timestamps();

   $table->index('question_id');
  });
 }


 /**
  * Reverse the migrations.
  */
 public function down(): void
 {
  Schema::dropIfExists('question_marking_schemes');
 }
};
