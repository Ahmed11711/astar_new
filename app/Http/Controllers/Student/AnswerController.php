<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Http\Requests\Student\Answer\SaveAnswerRequest;
use App\Models\answer;
use App\Models\StudentAttamp;
use App\Http\Service\AnswerService\AnswerService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Request;

class AnswerController extends Controller
{
    public function saveAnswersOptimized(SaveAnswerRequest $request)
    {
        Log::alert('saveAnswersOptimized called', $request->all());
        $userId    = $request->user_id;
        $attemptId = $request->attempt_id;
        $is_saved  = $request->is_saved;

        // Check attempt ownership
        $attempt = StudentAttamp::where('id', $attemptId)
            ->where('user_id', $userId)
            ->first();



        if (! $attempt) {
            return response()->json([
                'message' => 'Attempt not found or does not belong to this user'
            ], 404);
        }

        // Answers data & files
        $answersData  = $request->input('answers', []);
        $answersFiles = $request->files->get('answers', []);

        //  File paths configuration
        $paths = [
            'drawing_answer' => [
                'folder' => public_path('storage/answers/drawings'),
                'url'    => 'public/storage/answers/drawings',
                'prefix' => 'draw_',
            ],
            'audio_answer' => [
                'folder' => public_path('storage/answers/audio'),
                'url'    => 'public/storage/answers/audio',
                'prefix' => 'audio_',
            ],
        ];

        //  Ensure folders exist
        foreach ($paths as $config) {
            if (! file_exists($config['folder'])) {
                mkdir($config['folder'], 0755, true);
            }
        }

        $upsertData = [];


        $operationTime = now();

        foreach ($answersData as $index => $answer) {

            $response = $answer['response'] ?? [];

            foreach ($paths as $key => $config) {

                if (
                    isset($answersFiles[$index]['response'][$key]) &&
                    $answersFiles[$index]['response'][$key]->isValid()
                ) {
                    $file = $answersFiles[$index]['response'][$key];
                    $ext  = $file->getClientOriginalExtension();

                    $fileName = uniqid($config['prefix']) . '.' . $ext;

                    // Move file
                    $file->move($config['folder'], $fileName);

                    // Save correct URL in response
                    $response[$key] = url($config['url'] . '/' . $fileName);
                }
            }

            $upsertData[] = [
                'attempt_id'     => $attemptId,
                'user_id'        => $userId,
                'question_id'    => $answer['question_id'],
                'question_index' => $answer['question_index'],
                'response'       => json_encode($response, JSON_UNESCAPED_UNICODE),
                'is_flagged'     => $answer['is_flagged'] ?? false,
                'created_at'     => $operationTime,
                'updated_at'     => $operationTime,
            ];
            Log::info('Prepared answer data for upsert', $upsertData);
        }

        $answerIds = [];

        DB::transaction(function () use (
            $upsertData,
            $attemptId,
            $userId,
            $operationTime,
            &$answerIds
        ) {

            // Save answers
            answer::upsert(
                $upsertData,
                ['attempt_id', 'question_id', 'question_index', 'user_id'],
                ['response', 'is_flagged', 'updated_at']
            );

            // Mark attempt as saved
            StudentAttamp::where('id', $attemptId)
                ->update(['is_saved' => true]);

            //  ONLY answers touched in THIS request
            $answerIds = answer::where('attempt_id', $attemptId)
                ->where('user_id', $userId)
                ->where('updated_at', $operationTime)
                ->pluck('id')
                ->toArray();
        });

        //  Send to AI only when final save
        if ($is_saved && ! empty($answerIds)) {


            Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post('https://ai.astar.click/get_marks', [
                'answer_ids' => $answerIds,
            ]);
        }

        return response()->json([
            'message'    => 'All answers saved successfully.',
            'answer_ids' => $answerIds,
        ]);
    }

    // public function saveAnswersOptimizeds(SaveAnswerRequest $request, AnswerService $service)
    // {
    //     $validated = $request->validated();

    //     $validated['user_id'] = $request->user_id;

    //     $result = $service->saveWithAttempt($validated);

    //     if ($request->is_saved) {
    //         // dispatch(new SendAnswersToAIJob($result['answer_ids']));
    //     }
    //     return $result;

    //     return response()->json($result);
    // }



    // public function saveAnswersAutoAttempt(SaveAnswerRequest $request, AnswerService $service)
    // {
    //     $validated = $request->validated();

    //     $validated['user_id'] = $request->user_id;
    //     return $result = $service->saveAutoAttempt($validated);

    //     if ($request->is_saved) {
    //         // dispatch(new SendAnswersToAIJob($result['answer_ids']));
    //     }

    //     return response()->json($result);
    // }



    public function saveAnswersAutoAttempt(SaveAnswerRequest $request)
    {
        // Log::alert('saveAnswersAutoAttempt called', $request->all());

        $userId   = $request->user_id;
        $isSaved  = $request->is_saved;
        $answers  = $request->input('answers', []);

        if (empty($answers)) {
            return response()->json([
                'message' => 'No answers provided'
            ], 422);
        }


        $questionIds = collect($answers)
            ->pluck('question_id')
            ->unique()
            ->values();


        $questions = DB::table('questions')
            ->whereIn('id', $questionIds)
            ->select('id', 'exam_paper_id')
            ->get()
            ->keyBy('id');

        if ($questions->count() !== $questionIds->count()) {
            return response()->json([
                'message' => 'One or more questions not found'
            ], 404);
        }


        $groupedAnswers = collect($answers)->groupBy(function ($answer) use ($questions) {
            return $questions[$answer['question_id']]->exam_paper_id;
        });

        $result = [];
        $allAnswerIds = [];


        foreach ($groupedAnswers as $examPaperId => $answersGroup) {

            $examPaper = DB::table('exam_papers')
                ->where('id', $examPaperId)
                ->select('id', 'paper_id')
                ->first();

            if (! $examPaper) {
                continue;
            }


            $attempt = StudentAttamp::firstOrCreate(
                [
                    'user_id'       => $userId,
                    'exam_id' => $examPaper->id,
                ],
                [
                    'paper_id' => $examPaper->paper_id,
                    'is_saved' => false,
                ]
            );


            $newRequest = clone $request;

            $newRequest->merge([
                'attempt_id' => $attempt->id,
                'answers'    => $answersGroup->values()->toArray(),
                'is_saved'   => $isSaved,
            ]);


            $response = $this->saveAnswersOptimized($newRequest);

            return  $data = $response->getData(true);

            // if (! empty($data['answer_ids'])) {
            //     $allAnswerIds = array_merge($allAnswerIds, $data['answer_ids']);
            // }

            // $result[] = [
            //     'exam_paper_id' => $examPaper->id,
            //     'attempt_id'    => $attempt->id,
            //     'answer_ids'    => $data['answer_ids'] ?? [],
            // ];
        }

        // return response()->json([
        //     'message'    => 'All answers saved successfully.',
        //     'answer_ids' => $data,
        // ]);
    }
}
