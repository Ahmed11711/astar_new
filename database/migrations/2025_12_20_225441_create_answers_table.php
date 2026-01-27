<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('answers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('question_id')->nullable();
            $table->unsignedBigInteger('attempt_id');
            $table->json('response')->nullable();
            $table->boolean('is_correct')->default(false);
            $table->boolean('is_flagged')->default(false);
            $table->integer('question_index')->nullable();
            $table->decimal('awarded_marks', 5, 2)->nullable();
            $table->text('ai_feedback')->nullable();
            $table->text('teacher_feedback')->nullable();


            $table->timestamps();


            $table->index(['attempt_id', 'question_id']);
            $table->index('question_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('answers');
    }
};
