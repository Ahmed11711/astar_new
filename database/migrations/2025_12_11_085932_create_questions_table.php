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
        Schema::create('questions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('exam_paper_id')->constrained()->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained()->cascadeOnDelete();
            $table->foreignId('topic_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('subtopics_id')->nullable()->constrained()->nullOnDelete();

            $table->string('question_number')->nullable();
            $table->longText('question_string')->nullable();
            $table->longText('question_name')->nullable();

            $table->string('question_type');
            $table->integer('question_max_score')->nullable();

            $table->foreignId('parent_id')->nullable()->constrained('questions')->nullOnDelete();

            $table->boolean('has_options')->default(false);
            $table->json('marking_scheme')->nullable();
            $table->timestamps();

            // index for fast tree queries & ordering
            $table->index(['exam_paper_id', 'parent_id']);
            $table->index('question_number');
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};
