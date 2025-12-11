<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
 /**
  * Run the migrations.
  */
 public function up(): void
 {
  Schema::create('packages', function (Blueprint $table) {
   $table->id();
   $table->string('name');
   $table->text('description')->nullable();
   $table->decimal('price', 10, 2)->default(0);
   $table->integer('duration_days')->default(30);

   // Assignable (School / Teacher / System)
   $table->unsignedBigInteger('assignable_id')->nullable();
   $table->string('assign_type')->default('system'); // 'teacher','school','system'

   $table->timestamps();
  });
 }

 /**
  * Reverse the migrations.
  */
 public function down(): void
 {
  Schema::dropIfExists('packages');
 }
};
