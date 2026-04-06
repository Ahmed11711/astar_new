<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Http\Requests\Student\Attmpate\CreateAttmpateRequest;
use App\Models\StudentAttamp;
use App\Repositories\Attmpate\AttmpateRepositoryInterface;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

class AttmpateWithAnswerController extends Controller
{
    use ApiResponseTrait;

    public function __construct(public AttmpateRepositoryInterface $attemptRepo) {}



    public function createAttamepate(CreateAttmpateRequest $request)
    {
        $userId = $request->user_id;
        $data = $request->validated();

        $activeAttempt = StudentAttamp::query()
            ->where('user_id', $userId)
            ->where('exam_id', $data['exam_id'])
            ->where('is_saved', 0)
            ->first();

        if ($activeAttempt) {
            return $this->successResponse($activeAttempt, "Student already has an active attempt for this exam");
        }

        $attempt = StudentAttamp::create([
            'user_id'    => $userId,
            'exam_id'    => $data['exam_id'],
            'paper_id'   => $data['paper_id'],
            'grade_id'   => $data['grade_id'] ?? 1,
            'started_at' => now(),
            'is_saved'   => false,
        ]);

        return $this->successResponse($attempt);
    }

    public function poll(Request $request)
    {
        $userId    = $request->query('userId');
        $attemptId = $request->query('attemptId');

        $activeAttempt = StudentAttamp::query()
            ->where('user_id', $userId)
            ->where('id', $attemptId)
            ->first();

        $hasScore = false;

        if ($activeAttempt) {
            $hasScore = !is_null($activeAttempt->score)
                || !is_null($activeAttempt->max_score);
        }

        return response()->json([
            'update' => $hasScore
        ]);
    }
}
