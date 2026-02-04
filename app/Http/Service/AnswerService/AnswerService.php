<?php

namespace App\Http\Service\AnswerService;

use App\Models\answer;
use App\Models\StudentAttamp;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class AnswerService
{
    /**
     * 🔹 Save answers when attempt_id موجود
     */
    public function saveWithAttempt(array $payload): array
    {
        $attemptId = $payload['attempt_id'];
        $answers   = $payload['answers'];

        // map: question_id => attempt_id
        $attemptMap = [];
        foreach ($answers as $answer) {
            $attemptMap[$answer['question_id']] = $attemptId;
        }

        return $this->saveCore($payload, $attemptMap);
    }

    /**
     * 🔹 Save answers when attempt_id مش موجود (Auto-create)
     */
    public function saveAutoAttempt(array $payload): array
    {
        $userId  = $payload['user_id'];
        $answers = $payload['answers'];

        $questionIds = collect($answers)->pluck('question_id')->unique();

        $questions = DB::table('questions')
            ->whereIn('id', $questionIds)
            ->select('id', 'exam_paper_id')
            ->get()
            ->keyBy('id');

        $attemptMap = [];

        // 🔹 Group answers by exam_paper_id
        $grouped = collect($answers)->groupBy(
            fn($a) => $questions[$a['question_id']]->exam_paper_id
        );

        foreach ($grouped as $examPaperId => $group) {

            $examPaper = DB::table('exam_papers')
                ->where('id', $examPaperId)
                ->select('id', 'paper_id')
                ->first();

            if (!$examPaper) continue; // Safety check

            $attempt = StudentAttamp::firstOrCreate(
                [
                    'user_id' => $userId,
                    'exam_id' => $examPaper->id,
                ],
                [
                    'paper_id' => $examPaper->paper_id,
                    'is_saved' => false,
                ]
            );

            // map: question_id => attempt_id
            foreach ($group as $answer) {
                $attemptMap[$answer['question_id']] = $attempt->id;
            }
        }

        return $this->saveCore($payload, $attemptMap);
    }

    /**
     * 🔹 Core logic to upsert answers, handle files, and mark attempts as saved
     */
    private function saveCore(array $payload, array $attemptMap): array
    {
        $userId      = $payload['user_id'];
        $answers     = $payload['answers'];
        $isSaved     = $payload['is_saved'] ?? false;
        $answersFiles = $payload['files'] ?? []; // لو جاي من Request

        $now = now();
        $upserts = [];

        // 📁 File paths config
        $paths = [
            'drawing_answer' => [
                'folder' => public_path('storage/answers/drawings'),
                'url'    => 'storage/answers/drawings',
                'prefix' => 'draw_',
            ],
            'audio_answer' => [
                'folder' => public_path('storage/answers/audio'),
                'url'    => 'storage/answers/audio',
                'prefix' => 'audio_',
            ],
        ];

        // Ensure folders exist
        foreach ($paths as $config) {
            if (! file_exists($config['folder'])) {
                mkdir($config['folder'], 0755, true);
            }
        }

        DB::transaction(function () use ($answers, $attemptMap, $userId, $now, $paths, $answersFiles, $isSaved, &$upserts) {

            foreach ($answers as $answer) {
                $questionId = $answer['question_id'];
                $attemptId  = $attemptMap[$questionId] ?? null;
                if (!$attemptId) continue; // Safety

                $response = $answer['response'] ?? [];

                // Handle files
                if (isset($answersFiles[$questionId]['response'])) {
                    foreach ($paths as $key => $config) {
                        if (isset($answersFiles[$questionId]['response'][$key]) && $answersFiles[$questionId]['response'][$key]->isValid()) {
                            $file = $answersFiles[$questionId]['response'][$key];
                            $ext  = $file->getClientOriginalExtension();
                            $fileName = uniqid($config['prefix']) . '.' . $ext;
                            $file->move($config['folder'], $fileName);
                            $response[$key] = url($config['url'] . '/' . $fileName);
                        }
                    }
                }

                $upserts[] = [
                    'attempt_id'     => $attemptId,
                    'user_id'        => $userId,
                    'question_id'    => $questionId,
                    'question_index' => $answer['question_index'] ?? 0,
                    'response'       => json_encode($response, JSON_UNESCAPED_UNICODE),
                    'is_flagged'     => $answer['is_flagged'] ?? false,
                    'created_at'     => $now,
                    'updated_at'     => $now,
                ];
            }

            // 🔹 Batch upsert answers
            answer::upsert(
                $upserts,
                ['attempt_id', 'question_id', 'user_id'],
                ['response', 'is_flagged', 'updated_at']
            );

            // 🔹 Mark attempts as saved
            StudentAttamp::whereIn('id', array_values($attemptMap))
                ->update(['is_saved' => $isSaved]);
        });

        // 🔹 Get inserted/updated answer IDs
        $answerIds = answer::whereIn('attempt_id', array_values($attemptMap))
            ->where('updated_at', $now)
            ->pluck('id')
            ->toArray();

        // 🔹 Optional: send to AI if final save
        if ($isSaved && !empty($answerIds)) {
            Http::withHeaders(['Content-Type' => 'application/json'])
                ->post('https://ai.astar.click/get_marks', [
                    'answer_ids' => $answerIds,
                ]);
        }

        return [
            'attempt_ids' => array_values($attemptMap),
            'answer_ids'  => $answerIds,
        ];
    }
}
