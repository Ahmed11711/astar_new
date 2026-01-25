<?php

namespace App\Http\Controllers\Teacher\StudentChatAi;

use App\Http\Controllers\Controller;
use App\Http\Requests\Global\UserIdRequest;
use App\Http\Requests\Teacher\StudentChatAi\StudentChatAiShowRequest;
use App\Http\Requests\Teacher\StudentChatAi\updateStudentChatAiShowRequest;
use App\Models\chatAi;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

class StudentChateAiController extends Controller
{
    use ApiResponseTrait;
    public function index(UserIdRequest $request)
    {
        $data = $request->validated();
        $teacherId = $request->user_id;
        $studentId = $data['student_id'];

        // get chat ai for student paret_id null
        $chatAis = chatAi::where('user_id', $studentId)
            ->whereNull('parent_id')
            ->select('id', 'title', 'created_at')
            ->get();

        return    $this->successResponse(
            data: $chatAis,
            message: 'Chat AI for student retrieved successfully'
        );
    }

    public function show(StudentChatAiShowRequest $request)
    {
        $data = $request->validated();
        $teacherId = $request->user_id;
        $studentId = $data['student_id'];
        $chatId = $data['chat_id'];
        // get chat ai messages for student and chat id
        $chatAiMessages = chatAi::where('user_id', $studentId)
            ->where(function ($query) use ($chatId) {
                $query->where('id', $chatId)
                    ->orWhere('parent_id', $chatId);
            })
            ->select('id', 'parent_id', 'title', 'content', 'role', 'feedback', 'created_at')
            ->get();

        return    $this->successResponse(
            data: $chatAiMessages,
            message: 'Chat AI for student retrieved successfully'
        );
    }

    public function update(updateStudentChatAiShowRequest $request)
    {
        $data = $request->validated();
        $teacherId = $request->user_id;
        $studentId = $data['student_id'];
        $chatId = $data['chat_id'];
        // update chat ai title for student and chat id
        $chatAi = chatAi::where('user_id', $studentId)
            ->where('id', $chatId)
            ->first();
        $chatAi->update([
            'feedback' => $data['feedback'],
            // 'rating' => $data['rating'],
        ]);
        return    $this->successResponse(
            data: $chatAi,
            message: 'Chat AI for student updated successfully'
        );
    }
}
