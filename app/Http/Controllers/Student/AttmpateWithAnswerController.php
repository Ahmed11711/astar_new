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
            // ->whereNull('finished_at')
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
}
