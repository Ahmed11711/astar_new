<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class FeatureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();

        $features = [
            ['key' => 'chatAi', 'type' => 'number'],
            ['key' => 'Number of trials', 'type' => 'number'],
            ['key' => 'Number of exams', 'type' => 'number'],
            ['key' => 'Classified', 'type' => 'number'],
            ['key' => 'Automatic correction', 'type' => 'number'],
            ['key' => 'View latest trial (answer and mistakes)', 'type' => 'number'],
            ['key' => 'AI doubts', 'type' => 'number'],
            ['key' => 'AI chat in correction view', 'type' => 'number'],
            ['key' => 'attampted', 'type' => 'number'],
            ['key' => '50', 'type' => 'number'],
            ['key' => 'ddff', 'type' => 'boolean'],
        ];

        foreach ($features as $feature) {
            DB::table('features')->updateOrInsert(
                ['key' => $feature['key']],
                [
                    'type'       => $feature['type'],
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );
        }
    }
}
