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
  $answersData  = $request->input('answers', []);
$answersFiles = $request->files->get('answers', []);

// ✅ فولدرات ثابتة مسبقاً
$folders = [
    'drawing_answer' => public_path('storage/answers/drawings'),
    'audio_answer'   => public_path('storage/answers/audio'),
];

foreach ($folders as $f) {
    if (!file_exists($f)) mkdir($f, 0755, true);
}

$upsertData = [];
foreach ($answersData as $index => $a) {
    $response = $a['response'] ?? [];

    // ✅ معالجة الملفات بسرعة
    foreach (['drawing_answer', 'audio_answer'] as $key) {
        if (isset($answersFiles[$index]['response'][$key])
            && $answersFiles[$index]['response'][$key]->isValid()
        ) {
            $file = $answersFiles[$index]['response'][$key];
            $ext = $file->getClientOriginalExtension();
            $prefix = $key === 'drawing_answer' ? 'draw_' : 'audio_';
            $fileName = uniqid($prefix) . '.' . $ext;
            $file->move($folders[$key], $fileName);

            $response[$key] = url("public/storage/answers/{$key}/{$fileName}");
        }
    }

    $upsertData[] = [
        'attempt_id'     => $request->attempt_id,
        'user_id'        => $request->user_id,
        'question_id'    => $a['question_id'],
        'question_index' => $a['question_index'],
        'response'       => json_encode($response, JSON_UNESCAPED_UNICODE),
        'is_flagged'     => $a['is_flagged'] ?? false,
        'created_at'     => now(),
        'updated_at'     => now(),
    ];
}

// ✅ transaction سريع بدون انتظار الملفات
DB::transaction(function() use ($upsertData, $request) {
    answer::upsert(
        $upsertData,
        ['attempt_id', 'question_id', 'question_index', 'user_id'],
        ['response', 'is_flagged', 'updated_at']
    );

    StudentAttamp::where('id', $request->attempt_id)
        ->update(['is_saved' => $request->is_saved]);
});


        return response()->json([
            'detail' => 'All answers saved successfully.'
        ]);
    }
}
