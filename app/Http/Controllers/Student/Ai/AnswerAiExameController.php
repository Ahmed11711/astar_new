<?php

namespace App\Http\Controllers\Student\Ai;

use App\Http\Controllers\Controller;
use App\Http\Requests\Teacher\AnwserForUser\UpdateAnswerForUser;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AnswerAiExameController extends Controller
{
    use ApiResponseTrait;
    public function handelAiFeadback(Request $request)
    {
        $data = $request->json()->all();


        if (
            empty($data['job_id']) ||
            empty($data['results']) ||
            !is_array($data['results'])
        ) {
            return response()->json(['message' => 'Invalid AI payload'], 422);
        }

        DB::transaction(function () use ($data) {

            $attempts = [];
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

                    continue;
                }

                $score = $result['grading_score'] ?? 0;


                DB::table('answers')
                    ->where('id', $answer->id)
                    ->update([
                        'mark_score'  => $score,
                        'awarded_marks' => $score,
                        'is_correct'  => $result['is_correct'] ?? false,
                        'ai_feedback' => $result['feedback_message'] ?? null,
                        'updated_at'  => now(),
                    ]);

                if (!isset($attempts[$answer->attempt_id])) {
                    $attempts[$answer->attempt_id] = [
                        'user_id'     => $answer->user_id,
                        'total_score' => 0,
                    ];
                }

                $attempts[$answer->attempt_id]['total_score'] += $score;
            }

            foreach ($attempts as $attemptId => $data) {
                DB::table('student_attempts')
                    ->where('id', $attemptId)
                    ->where('user_id', $data['user_id'])
                    ->update([
                        'score'      => $data['total_score'],
                        'updated_at' => now(),
                    ]);
            }
        });

        return response()->json([
            'message' => 'AI grading processed successfully',
            'job_id'  => $data['job_id']
        ]);
    }





    public function handelTeacherFeedback(UpdateAnswerForUser $request)
    {
        $data = $request->validated();
        $answerId = $data['answer_id'];
        $teacher_feedback = $data['teacher_feedback'];
        $mark_score = $data['mark_score'];
        $is_correct = $data['is_correct'];

        $updated = DB::table('answers')
            ->where('id', $answerId)
            ->where('user_id', $data['student_id'])
            ->update([
                'teacher_feedback' => $teacher_feedback ?? null,
                'mark_score'       => $mark_score,
                'is_correct'       => $is_correct,
                'awarded_marks'    => $mark_score,
                'updated_at'       => now(),
            ]);

        if ($updated) {
            return $this->successResponse([], 'Teacher feedback updated successfully');
        } else {
            return $this->errorResponse('Answer not found for this student', 404);
        }
    }
}
