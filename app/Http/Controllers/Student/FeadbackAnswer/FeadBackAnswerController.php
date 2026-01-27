<?php

namespace App\Http\Controllers\Student\FeadbackAnswer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Student\Answer\FeadbackAnswerRequest;
use App\Models\answer;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

class FeadBackAnswerController extends Controller
{
    use ApiResponseTrait;
    public function index(FeadbackAnswerRequest $request)
    {
        $data = $request->validated();
        $userId = $request->user_id;
        // Logic to handle feedback answer submission
        $answer = answer::where('id', $data['answer_id'])->where('user_id', $userId)->first();
        if (!$answer) {
            return $this->errorResponse('Answer not found or does not belong to the user', 404);
        }
        $answer->user_feedback = $data['feadback'];
        $answer->save();
        return $this->successResponse([], 'Feedback submitted successfully', 200);
    }
}
