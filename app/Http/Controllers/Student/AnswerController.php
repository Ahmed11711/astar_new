<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Http\Requests\Student\Answer\SaveAnswerRequest;
use App\Models\answer;
use App\Models\StudentAttamp;
use Illuminate\Support\Facades\DB;

class AnswerController extends Controller
{
    public function saveAnswersOptimized(SaveAnswerRequest $request)
    {
        $userId = $request->user_id;

        $attempt = StudentAttamp::where('id', $request->attempt_id)
            ->where('user_id', $userId)
            ->first();

        if (! $attempt) {
            return response()->json([
                'message' => 'Attempt not found or does not belong to this user'
            ], 404);
        }

        // 🔹 Text / JSON data
        $answersData  = $request->input('answers', []);

        // 🔹 Files (drawing answers)
        $answersFiles = $request->files->get('answers', []);

        $upsertData = collect($answersData)->map(function ($a, $index) use (
            $attempt,
            $userId,
            $answersFiles
        ) {

            // response من الـ input
            $response = $a['response'] ?? [];

            // ✅ لو في صورة جاية فعلاً
if (
    isset($answersFiles[$index]['response']['drawing_answer'])
    && $answersFiles[$index]['response']['drawing_answer']->isValid()
) {
    $file = $answersFiles[$index]['response']['drawing_answer'];

    $fileName = uniqid('draw_') . '.' . $file->getClientOriginalExtension();

    $destinationPath = public_path('storage/answers/drawings');

    if (! file_exists($destinationPath)) {
        mkdir($destinationPath, 0755, true);
    }

    $file->move($destinationPath, $fileName);

    // ✅ مسار كامل
    $response['drawing_answer'] =
        url('public/storage/answers/drawings/' . $fileName);
}


            return [
                'attempt_id'     => $attempt->id,
                'user_id'        => $userId,
                'question_id'    => $a['question_id'],
                'question_index' => $a['question_index'],
                'response'       => json_encode($response, JSON_UNESCAPED_UNICODE),
                'is_flagged'     => $a['is_flagged'] ?? false,
                'created_at'     => now(),
                'updated_at'     => now(),
            ];
        })->toArray();

        DB::transaction(function () use ($upsertData, $attempt, $request) {
            answer::upsert(
                $upsertData,
                ['attempt_id', 'question_id', 'question_index', 'user_id'],
                ['response', 'is_flagged', 'updated_at']
            );

            $attempt->update([
                'is_saved' => $request->is_saved
            ]);
        });

        return response()->json([
            'detail' => 'All answers saved successfully.'
        ]);
    }
}
