<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Http\Requests\Student\Answer\SaveAnswerRequest;
use App\Models\answer;
use App\Models\StudentAttamp;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class AnswerController extends Controller
{
    public function saveAnswersOptimized(SaveAnswerRequest $request)
    {
        $userId = $request->user_id;
        $attemptId = $request->attempt_id;

        // 🔹 Check attempt ownership
        $attempt = StudentAttamp::where('id', $attemptId)
            ->where('user_id', $userId)
            ->first();

        if (! $attempt) {
            return response()->json([
                'message' => 'Attempt not found or does not belong to this user'
            ], 404);
        }

        // 🔹 Answers data & files
        $answersData  = $request->input('answers', []);
        $answersFiles = $request->files->get('answers', []);

        // 🔹 File paths configuration
        $paths = [
            'drawing_answer' => [
                'folder' => public_path('storage/answers/drawings'),
                'url'    => 'public/storage/answers/drawings',
                'prefix' => 'draw_',
            ],
            'audio_answer' => [
                'folder' => public_path('storage/answers/audio'),
                'url'    => 'public/storage/answers/audio',
                'prefix' => 'audio_',
            ],
        ];

        // 🔹 Ensure folders exist
        foreach ($paths as $config) {
            if (! file_exists($config['folder'])) {
                mkdir($config['folder'], 0755, true);
            }
        }

        $upsertData = [];

        foreach ($answersData as $index => $answer) {

            $response = $answer['response'] ?? [];

            foreach ($paths as $key => $config) {

                if (
                    isset($answersFiles[$index]['response'][$key]) &&
                    $answersFiles[$index]['response'][$key]->isValid()
                ) {
                    $file = $answersFiles[$index]['response'][$key];
                    $ext  = $file->getClientOriginalExtension();

                    $fileName = uniqid($config['prefix']) . '.' . $ext;

                    // 🔹 Move file
                    $file->move($config['folder'], $fileName);

                    // 🔹 Save correct URL in response
                    $response[$key] = url($config['url'] . '/' . $fileName);
                }
            }

            $upsertData[] = [
                'attempt_id'     => $attemptId,
                'user_id'        => $userId,
                'question_id'    => $answer['question_id'],
                'question_index' => $answer['question_index'],
                'response'       => json_encode($response, JSON_UNESCAPED_UNICODE),
                'is_flagged'     => $answer['is_flagged'] ?? false,
                'created_at'     => now(),
                'updated_at'     => now(),
            ];
        }

        $answerIds = [];

        DB::transaction(function () use ($upsertData, $attemptId, $userId, &$answerIds) {

            // 🔹 Save answers
            answer::upsert(
                $upsertData,
                ['attempt_id', 'question_id', 'question_index', 'user_id'],
                ['response', 'is_flagged', 'updated_at']
            );

            // 🔹 Mark attempt as saved
            StudentAttamp::where('id', $attemptId)
                ->update(['is_saved' => true]);

            // 🔹 Collect all answer IDs
            $answerIds = answer::where('attempt_id', $attemptId)
                ->where('user_id', $userId)
                ->pluck('id')
                ->toArray();
        });

        // 🔹 Send to AI service
        Http::withHeaders([
            'Content-Type' => 'application/json',
        ])
            ->post('https://ai.astar.click/get_marks', [
                'answer_ids' => $answerIds,
            ]);

        return response()->json([
            'message'    => 'All answers saved successfully.',
            'answer_ids' => $answerIds,
        ]);
    }
}
