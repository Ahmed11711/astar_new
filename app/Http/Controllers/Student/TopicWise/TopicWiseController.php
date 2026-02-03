<?php

namespace App\Http\Controllers\Student\TopicWise;

use App\Http\Controllers\Controller;
use App\Http\Requests\Student\TopicWise\createTopicWiseRequest;
use App\Http\Requests\Student\TopicWise\TopicWiseRequest;
use App\Models\Question;

class TopicWiseController extends Controller
{
    public function index(TopicWiseRequest $request)
    {
        $userId = $request->user_id;
        $data = $request->validated();

        $query = Question::with([
            'options',
            'audios',
            'images',
            // 'lastAttempt' => function ($query) use ($userId) {
            //     $query->where('user_id', $userId);
            // },
            'lastAnswer' => function ($query) use ($userId) {
                $query->where('user_id', $userId);
            },
        ]);

        if (isset($data['subject_id'])) {
            $query->where('subject_id', $data['subject_id']);
        }

        if (isset($data['topic_id'])) {
            $query->where('topic_id', $data['topic_id']);
        }

        if (isset($data['subtopic_id'])) {
            $query->where('subtopics_id', $data['subtopic_id']);
        }

        if (isset($data['count'])) {
            $query->limit($data['count']);
        }

        $questions = $query->get();

        return response()->json([
            'count' => $questions->count(),
            'questions' => $questions
        ]);
    }

    public function store(createTopicWiseRequest $request)
    {
        $userId = $request->user_id;
    }
}
