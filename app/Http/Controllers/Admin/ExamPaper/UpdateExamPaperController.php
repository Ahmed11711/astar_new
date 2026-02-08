<?php

namespace App\Http\Controllers\Admin\ExamPaper;

use App\Http\Controllers\Controller;
use App\Http\Requests\ExamPaperRequest;
use App\Http\Service\ExamPaperService;
use App\Repositories\ExamPaper\ExamPaperRepositoryInterface;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UpdateExamPaperController extends Controller
{
    use ApiResponseTrait;
    protected ExamPaperRepositoryInterface $repository;

    public function __construct(ExamPaperRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }
    public function store(ExamPaperRequest $request, ExamPaperService $service)
    {
        $paper = $service->createExamPaperWithQuestions($request->validated());
        return $this->successResponse($paper);
    }

    public function show(Request $request, $id)
    {
        return  $record = $this->repository->allData($id);
    }

    // main function
    // public function update(Request $request, $id)
    // {
    //     $data = $request->validated();

    //     DB::beginTransaction();

    //     try {
    //         $paper = $this->repository->find($id);
    //         if (!$paper) {
    //             return $this->errorResponse('ExamPaper not found', 404);
    //         }
    //         $paper->update([
    //             'title' => $data['title'] ?? $paper->title,
    //             'subject_id' => $data['subject_id'] ?? $paper->subject_id,
    //             'grade_id' => $data['grade_id'] ?? $paper->grade_id,
    //             'paper_id' => $data['paper_id'] ?? $paper->paper_id,
    //             'year' => $data['year'] ?? $paper->year,
    //             'month' => $data['month'] ?? $paper->month,
    //             'is_active' => $data['is_active'] ?? $paper->is_active,
    //             'total_marks' => $data['total_marks'] ?? $paper->total_marks,
    //             'duration_minutes' => $data['duration_minutes'] ?? $paper->duration_minutes,
    //         ]);

    //         if (isset($data['questions']) && is_array($data['questions'])) {
    //             foreach ($data['questions'] as $qData) {
    //                 $question = $paper->questions()->updateOrCreate(
    //                     ['id' => $qData['id'] ?? null],
    //                     [
    //                         'subject_id' => $qData['subject_id'],
    //                         'topic_id' => $qData['topic_id'],
    //                         'subtopics_id' => $qData['subtopics_id'],
    //                         'question_number' => $qData['question_number'],
    //                         'question_string' => $qData['question_string'],
    //                         'question_type' => $qData['question_type'],
    //                         'question_max_score' => $qData['question_max_score'],
    //                         'parent_id' => $qData['parent_id'] ?? null,
    //                         'has_options' => $qData['has_options'] ?? 0,
    //                         'marking_scheme' => $qData['marking_scheme'] ?? [],
    //                     ]
    //                 );

    //                 if (isset($qData['option'])) {
    //                     $optionIds = [];
    //                     foreach ($qData['option'] as $optData) {
    //                         $option = $question->option()->updateOrCreate(
    //                             ['id' => $optData['id'] ?? null],
    //                             [
    //                                 'text' => $optData['text'],
    //                                 'is_correct' => $optData['is_correct'] ?? 0,
    //                                 'order' => $optData['order'] ?? null
    //                             ]
    //                         );
    //                         $optionIds[] = $option->id;
    //                     }
    //                     $question->option()->whereNotIn('id', $optionIds)->delete();
    //                 }
    //             }
    //         }

    //         DB::commit();

    //         $updated = $this->repository->allData($id);
    //         return $this->successResponse($updated, 'ExamPaper updated successfully');
    //     } catch (\Throwable $e) {
    //         DB::rollBack();
    //         return $this->errorResponse('Failed to update ExamPaper: ' . $e->getMessage(), 500);
    //     }
    // }


    public function update(Request $request, $id)
    {
        $data = $request->all();
        DB::beginTransaction();

        try {
            $paper = $this->repository->find($id);
            if (!$paper) {
                Log::warning("ExamPaper with ID {$id} not found");
                return $this->errorResponse('ExamPaper not found', 404);
            }

            // =========================
            // Update Paper
            // =========================
            Log::info("Updating ExamPaper ID {$id}");
            $paper->update([
                'title'            => $data['title'] ?? $paper->title,
                'subject_id'       => $data['subject_id'] ?? $paper->subject_id,
                'grade_id'         => $data['grade_id'] ?? $paper->grade_id,
                'paper_id'         => $data['paper_id'] ?? $paper->paper_id,
                'year'             => $data['year'] ?? $paper->year,
                'month'            => $data['month'] ?? $paper->month,
                'is_active'        => $data['is_active'] ?? $paper->is_active,
                'total_marks'      => $data['total_marks'] ?? $paper->total_marks,
                'duration_minutes' => $data['duration_minutes'] ?? $paper->duration_minutes,
            ]);

            Log::info("Paper updated successfully");

            // =========================
            // Update or Insert Questions
            // =========================
            if (!empty($data['questions']) && is_array($data['questions'])) {
                foreach ($data['questions'] as $qData) {
                    $questionId = $qData['id'] ?? $qData['question_id'] ?? null;

                    if ($questionId) {
                        $question = $paper->questions()->where('id', $questionId)->first();
                        if ($question) {
                            Log::info("Updating Question ID {$questionId}");
                            $question->update([
                                'subject_id'         => $qData['subject_id'] ?? $paper->subject_id,
                                'topic_id'           => $qData['topic_id'] ?? null,
                                'subtopics_id'       => $qData['subtopics_id'] ?? null,
                                'question_number'    => $qData['question_number'] ?? null,
                                'question_string'    => $qData['question_string'] ?? null,
                                'question_type'      => $qData['question_type'] ?? 'mcq',
                                'question_max_score' => $qData['question_max_score'] ?? 0,
                                'parent_id'          => $qData['parent_id'] ?? null,
                                'has_options'        => !empty($qData['options']),
                                'marking_scheme'     => $qData['marking_scheme'] ?? [],
                                'is_text_only'       => $qData['is_text_only'] ?? 0,
                            ]);
                        } else {
                            Log::info("Question ID {$questionId} not found, creating new one");
                            $question = $paper->questions()->create([
                                'subject_id'         => $qData['subject_id'] ?? $paper->subject_id,
                                'topic_id'           => $qData['topic_id'] ?? null,
                                'subtopics_id'       => $qData['subtopics_id'] ?? null,
                                'question_number'    => $qData['question_number'] ?? null,
                                'question_string'    => $qData['question_string'] ?? null,
                                'question_type'      => $qData['question_type'] ?? 'mcq',
                                'question_max_score' => $qData['question_max_score'] ?? 0,
                                'parent_id'          => $qData['parent_id'] ?? null,
                                'has_options'        => !empty($qData['options']),
                                'marking_scheme'     => $qData['marking_scheme'] ?? [],
                                'is_text_only'       => $qData['is_text_only'] ?? 0,
                            ]);
                        }
                    } else {
                        Log::info("Creating new Question with number {$qData['question_number']}");
                        $question = $paper->questions()->create([
                            'subject_id'         => $qData['subject_id'] ?? $paper->subject_id,
                            'topic_id'           => $qData['topic_id'] ?? null,
                            'subtopics_id'       => $qData['subtopics_id'] ?? null,
                            'question_number'    => $qData['question_number'] ?? null,
                            'question_string'    => $qData['question_string'] ?? null,
                            'question_type'      => $qData['question_type'] ?? 'mcq',
                            'question_max_score' => $qData['question_max_score'] ?? 0,
                            'parent_id'          => $qData['parent_id'] ?? null,
                            'has_options'        => !empty($qData['options']),
                            'marking_scheme'     => $qData['marking_scheme'] ?? [],
                            'is_text_only'       => $qData['is_text_only'] ?? 0,
                        ]);
                    }

                    // =========================
                    // Update or Insert Options
                    // =========================
                    if (!empty($qData['options']) && is_array($qData['options'])) {
                        $optionIds = [];
                        foreach ($qData['options'] as $optData) {
                            $opt = $question->option()->updateOrCreate(
                                ['id' => $optData['id'] ?? null],
                                [
                                    'text'       => $optData['text'] ?? null,
                                    'is_correct' => $optData['is_correct'] ?? 0,
                                    'order'      => $optData['order'] ?? null,
                                ]
                            );
                            $optionIds[] = $opt->id;
                        }
                        $question->option()->whereNotIn('id', $optionIds)->delete();
                        Log::info("Options updated for Question ID {$question->id}");
                    }

                    // =========================
                    // Recursive Sub Questions
                    // =========================
                    if (!empty($qData['sub_questions'])) {
                        $this->updateSubQuestions($question, $qData['sub_questions']);
                    }
                }
            }

            DB::commit();

            $updatedPaper = $paper->load(['questions.option']);
            Log::info("ExamPaper ID {$id} updated successfully with all questions");

            return $this->successResponse($updatedPaper, 'ExamPaper updated successfully');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error("Failed to update ExamPaper ID {$id}: {$e->getMessage()}");
            return $this->errorResponse('Failed to update ExamPaper: ' . $e->getMessage(), 500);
        }
    }

    // =========================
    // Helper for Sub Questions
    // =========================
    private function updateSubQuestions($parentQuestion, $subQuestions)
    {
        Log::info("SubQuestion ID {$parentQuestion} updated");

        foreach ($subQuestions as $sq) {
            $subQId = $sq['id'] ?? $sq['question_id'] ?? null;

            if ($subQId) {
                $subQ = $parentQuestion->subQuestions()->where('id', $subQId)->first();
                if ($subQ) {
                    $subQ->update([
                        'subject_id'         => $sq['subject_id'] ?? $parentQuestion->subject_id,
                        'topic_id'           => $sq['topic_id'] ?? $parentQuestion->topic_id,
                        'subtopics_id'       => $sq['subtopics_id'] ?? $parentQuestion->subtopics_id,
                        'question_number'    => $sq['question_number'] ?? null,
                        'question_string'    => $sq['question_string'] ?? null,
                        'question_type'      => $sq['question_type'] ?? 'mcq',
                        'question_max_score' => $sq['question_max_score'] ?? 0,
                        'parent_id'          => $parentQuestion->id,
                        'has_options'        => !empty($sq['options']),
                        'marking_scheme'     => $sq['marking_scheme'] ?? [],
                        'is_text_only'       => $sq['is_text_only'] ?? 0,
                    ]);
                    Log::info("SubQuestion ID {$subQId} updated");
                } else {
                    $subQ = $parentQuestion->subQuestions()->create([
                        'subject_id'         => $sq['subject_id'] ?? $parentQuestion->subject_id,
                        'topic_id'           => $sq['topic_id'] ?? $parentQuestion->topic_id,
                        'subtopics_id'       => $sq['subtopics_id'] ?? $parentQuestion->subtopics_id,
                        'question_number'    => $sq['question_number'] ?? null,
                        'question_string'    => $sq['question_string'] ?? null,
                        'question_type'      => $sq['question_type'] ?? 'mcq',
                        'question_max_score' => $sq['question_max_score'] ?? 0,
                        'parent_id'          => $parentQuestion->id,
                        'has_options'        => !empty($sq['options']),
                        'marking_scheme'     => $sq['marking_scheme'] ?? [],
                        'is_text_only'       => $sq['is_text_only'] ?? 0,
                    ]);
                    Log::info("SubQuestion ID {$subQ->id} created");
                }
            } else {
                $subQ = $parentQuestion->subQuestions()->create([
                    'subject_id'         => $sq['subject_id'] ?? $parentQuestion->subject_id,
                    'topic_id'           => $sq['topic_id'] ?? $parentQuestion->topic_id,
                    'subtopics_id'       => $sq['subtopics_id'] ?? $parentQuestion->subtopics_id,
                    'question_number'    => $sq['question_number'] ?? null,
                    'question_string'    => $sq['question_string'] ?? null,
                    'question_type'      => $sq['question_type'] ?? 'mcq',
                    'question_max_score' => $sq['question_max_score'] ?? 0,
                    'parent_id'          => $parentQuestion->id,
                    'has_options'        => !empty($sq['options']),
                    'marking_scheme'     => $sq['marking_scheme'] ?? [],
                    'is_text_only'       => $sq['is_text_only'] ?? 0,
                ]);
                Log::info("SubQuestion ID {$subQ->id} created");
            }

            // Update Options for subQuestions
            if (!empty($sq['options'])) {
                $optionIds = [];
                foreach ($sq['options'] as $optData) {
                    $opt = $subQ->option()->updateOrCreate(
                        ['id' => $optData['id'] ?? null],
                        [
                            'text'       => $optData['text'] ?? null,
                            'is_correct' => $optData['is_correct'] ?? 0,
                            'order'      => $optData['order'] ?? null,
                        ]
                    );
                    $optionIds[] = $opt->id;
                }
                $subQ->option()->whereNotIn('id', $optionIds)->delete();
                Log::info("Options updated for SubQuestion ID {$subQ->id}");
            }

            // Recursive deeper
            if (!empty($sq['sub_questions'])) {
                $this->updateSubQuestions($subQ, $sq['sub_questions']);
            }
        }
    }
}
