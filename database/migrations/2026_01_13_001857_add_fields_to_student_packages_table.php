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
        Schema::table('student_packages', function (Blueprint $table) {
            $table->enum('status', ['pending', 'paid', 'refund'])
                ->default('pending')
                ->after('id');

            $table->enum('type', ['free', 'not_free'])
                ->default('free')
                ->after('status');

            $table->decimal('price', 10, 2)
                ->nullable()
                ->after('type');

            $table->string('transaction_id')
                ->nullable()
                ->unique()
                ->after('price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('student_packages', function (Blueprint $table) {
            //
        });
    }
};
