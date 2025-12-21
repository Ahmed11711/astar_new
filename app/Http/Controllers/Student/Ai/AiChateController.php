<?php

namespace App\Http\Controllers\Student\Ai;

use App\Http\Controllers\Controller;
use App\Http\Requests\Student\Ai\AiChateRequest;
use App\Models\ChatAi;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class AiChateController extends Controller
{
    use ApiResponseTrait;
    public function index(Request $request)
    {
        $userId = $request->user_id;

        return Cache::remember(
            "chat_ai_user_{$userId}",
            now()->addMinutes(5),
            function () use ($userId) {
                return ChatAi::where('user_id', $userId)
                    ->orderBy('id')
                    ->get();
            }
        );
    }

    /**
     * Store new message (user or assistant)
     */
    public function store(AiChateRequest $request)
    {
        $data = $request->validated();
        $userId = $request->user_id;

        $data = $request->$data['user_id'] = $userId;

        $chat = ChatAi::create($data);

        // clear cache
        Cache::forget("chat_ai_user_" . $userId);

        return $this->successResponsePaginate($chat);
    }

    /**
     * Show single message
     */
    public function show(Request $request, $id)
    {
        $userId = $request->user_id;

        return ChatAi::where('id', $id)
            ->where('user_id', $userId)
            ->firstOrFail();
    }

    /**
     * Update message (rating only)
     */
    public function update(Request $request, $id)
    {
        $userId = $request->user_id;


        $chat = ChatAi::where('id', $id)
            ->where('user_id', $userId)
            ->firstOrFail();

        $data = $request->validate([
            'rating' => 'nullable|in:like,dislike',
        ]);

        $chat->update($data);

        Cache::forget("chat_ai_user_" . $userId);
        return $this->successResponse($chat);
    }

    /**
     * Delete conversation branch
     */
    public function destroy(Request $request, $id)
    {
        $userId = $request->user_id;

        $chat = ChatAi::where('id', $id)
            ->where('user_id', $userId)
            ->firstOrFail();

        $chat->delete();

        Cache::forget("chat_ai_user_" . $userId);

        return response()->json(['message' => 'Deleted successfully']);
    }
}
