<?php

namespace App\Http\Controllers\Student\Ai;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class AnswerAiExameController extends Controller
{
    public function handle(Request $request)
    {
        $data = $request->json()->all();

        Log::info('AI Payload', $data);

        // Validate payload structure
        if (
            !isset($data['job_id']) ||
            !isset($data['status']) ||
            !isset($data['results']) ||
            !is_array($data['results'])
        ) {
            return response()->json([
                'message' => 'Invalid AI payload'
            ], 422);
        }

        $jobId   = $data['job_id'];
        $status  = $data['status'];
        $results = $data['results'];

        foreach ($results as $result) {

            if (!isset($result['answer_id'])) {
                continue;
            }

            DB::table('answers')
                ->where('id', $result['answer_id'])
                ->update([
                    'score'        => $result['grading_score'] ?? 0,
                    'is_correct'   => $result['is_correct'] ?? false,
                    'ai_feedback'  => $result['feedback_message'] ?? null,
                    // 'ai_job_id'    => $jobId,
                    // 'ai_status'    => $status,
                    'updated_at'   => now(),
                ]);
        }

        return response()->json([
            'message' => 'Answers updated successfully',
            'job_id'  => $jobId
        ], 200);
    }
}
