<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Http\Requests\Student\Attmpate\CreateAttmpateRequest;
use App\Models\StudentAttamp;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

class AttmpateWithAnswerController extends Controller
{
    use ApiResponseTrait;

    public function createAttamepate(CreateAttmpateRequest $request)
    {
        $data = $request->validated();
        $userId = $request->user_id;
        $gradeId = $request->grade_id;
        $attemptData = [
            'user_id' => $userId,
            'exam_id' => $data['exam_id'],
            'paper_id' => $data['paper_id'],
            'grade_id' => $gradeId ?? 1,
            'started_at' => now(),
            'is_saved' => false,
        ];
        $attempt = StudentAttamp::create($attemptData);
        return $this->successResponse($attempt);
    }
}
