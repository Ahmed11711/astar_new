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
  Schema::create('question_options', function (Blueprint $table) {
   $table->id();
   $table->foreignId('question_id')->constrained()->cascadeOnDelete();
   $table->text('text');
   $table->boolean('is_correct')->default(false);
   $table->integer('order')->nullable(); // ترتيب الخيار للعرض
   $table->timestamps();

   $table->index('question_id');
  });
 }


 /**
  * Reverse the migrations.
  */
 public function down(): void
 {
  Schema::dropIfExists('question_options');
 }
};
