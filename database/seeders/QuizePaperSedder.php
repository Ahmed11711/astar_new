<?php

namespace Database\Seeders;

use App\Models\paper;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class QuizePaperSedder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        paper::create([
            'name' => 'quiz Exam',
            'type' => 'quiz',

        ]);
    }
}
