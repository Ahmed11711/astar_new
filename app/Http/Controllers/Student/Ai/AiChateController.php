<?php

namespace App\Http\Controllers\Student\Ai;

use App\Http\Controllers\Controller;
use App\Http\Requests\Student\Ai\AiChateRequest;
use App\Models\chatAi;
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
                $parentChats = chatAi::where('user_id', $userId)
                    ->whereNull('parent_id')
                    ->get();

                return response()->json($parentChats);
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

        // صح، ضيف user_id للمصفوفة
        $data['user_id'] = $userId;

        $chat = chatAi::create($data);

        // clear cache
        Cache::forget("chat_ai_user_" . $userId);

        return $this->successResponse($chat);
    }
    /**
     * Show single message
     */
    public function show(Request $request, $id)
    {
        $userId = $request->user_id;

        return chatAi::where('id', $id)
            ->where('user_id', $userId)
            ->with('replies')
            ->firstOrFail();
    }

    /**
     * Update message (rating only)
     */
    public function update(Request $request, $id)
    {
        $userId = $request->user_id;


        $chat = chatAi::where('id', $id)
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

        $chat = chatAi::where('id', $id)
            ->where('user_id', $userId)
            ->firstOrFail();

        $chat->delete();

        Cache::forget("chat_ai_user_" . $userId);

        return response()->json(['message' => 'Deleted successfully']);
    }
}
