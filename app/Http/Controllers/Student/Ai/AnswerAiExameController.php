<?php

namespace App\Http\Controllers\Student\Ai;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class AnswerAiExameController extends Controller
{
    public function handelAiFeadback(Request $request)
    {
        $data = $request->json()->all();

        Log::info('AI Payload', $data);

        if (
            empty($data['job_id']) ||
            empty($data['results']) ||
            !is_array($data['results'])
        ) {
            return response()->json(['message' => 'Invalid AI payload'], 422);
        }

        DB::transaction(function () use ($data) {

            $attempts = []; // attempt_id => ['user_id' => ?, 'score' => ?]

            foreach ($data['results'] as $result) {

                if (empty($result['answer_id'])) {
                    continue;
                }

                $answer = DB::table('answers')
                    ->select('id', 'attempt_id', 'user_id')
                    ->where('id', $result['answer_id'])
                    ->lockForUpdate()
                    ->first();

                if (!$answer) {
                    Log::warning('Answer not found', [
                        'answer_id' => $result['answer_id']
                    ]);
                    continue;
                }

                $score = $result['grading_score'] ?? 0;

                // تحديث جدول الأجوبة
                DB::table('answers')
                    ->where('id', $answer->id)
                    ->update([
                        'mark_score'  => $score,
                        'is_correct'  => $result['is_correct'] ?? false,
                        'ai_feedback' => $result['feedback_message'] ?? null,
                        'updated_at'  => now(),
                    ]);

                // حفظ attempt info لتحديثه بعدين
                $attempts[$answer->attempt_id] = [
                    'user_id' => $answer->user_id,
                    'score'   => $score,
                ];
            }

            // تحديث student_attempts
            foreach ($attempts as $attemptId => $data) {
                DB::table('student_attempts')
                    ->where('id', $attemptId)
                    ->where('user_id', $data['user_id'])
                    ->update([
                        // 'ai_checked' => true,
                        'score' => $data['score'],
                        'updated_at' => now(),
                    ]);
            }
        });

        return response()->json([
            'message' => 'AI grading processed successfully',
            'job_id'  => $data['job_id']
        ]);
    }



    public function handelTeacherFeedback(Request $request)
    {
        $data = $request->json()->all();

        Log::info('Teacher Feedback Payload', $data);

        // Validate payload structure
        if (
            !isset($data['answers']) ||
            !is_array($data['answers'])
        ) {
            return response()->json([
                'message' => 'Invalid Teacher Feedback payload'
            ], 422);
        }

        $answers = $data['answers'];

        foreach ($answers as $answer) {

            if (!isset($answer['answer_id'])) {
                continue;
            }

            DB::table('answers')
                ->where('id', $answer['answer_id'])
                ->update([
                    'teacher_feedback' => $answer['teacher_feedback'] ?? null,
                    'updated_at'       => now(),
                ]);
        }

        return response()->json([
            'message' => 'Teacher feedback updated successfully',
        ], 200);
    }
}
