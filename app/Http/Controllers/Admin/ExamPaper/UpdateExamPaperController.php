<?php

namespace App\Http\Controllers\Admin\ExamPaper;

use App\Http\Controllers\Controller;
use App\Http\Requests\ExamPaperRequest;
use App\Http\Service\ExamPaperService;
use App\Repositories\ExamPaper\ExamPaperRepositoryInterface;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


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

    public function update(Request $request, $id)
    {
        $data = $request->validated();

        DB::beginTransaction();

        try {
            $paper = $this->repository->find($id);
            if (!$paper) {
                return $this->errorResponse('ExamPaper not found', 404);
            }
            $paper->update([
                'title' => $data['title'] ?? $paper->title,
                'subject_id' => $data['subject_id'] ?? $paper->subject_id,
                'grade_id' => $data['grade_id'] ?? $paper->grade_id,
                'paper_id' => $data['paper_id'] ?? $paper->paper_id,
                'year' => $data['year'] ?? $paper->year,
                'month' => $data['month'] ?? $paper->month,
                'is_active' => $data['is_active'] ?? $paper->is_active,
                'total_marks' => $data['total_marks'] ?? $paper->total_marks,
                'duration_minutes' => $data['duration_minutes'] ?? $paper->duration_minutes,
            ]);

            if (isset($data['questions']) && is_array($data['questions'])) {
                foreach ($data['questions'] as $qData) {
                    $question = $paper->questions()->updateOrCreate(
                        ['id' => $qData['id'] ?? null],
                        [
                            'subject_id' => $qData['subject_id'],
                            'topic_id' => $qData['topic_id'],
                            'subtopics_id' => $qData['subtopics_id'],
                            'question_number' => $qData['question_number'],
                            'question_string' => $qData['question_string'],
                            'question_type' => $qData['question_type'],
                            'question_max_score' => $qData['question_max_score'],
                            'parent_id' => $qData['parent_id'] ?? null,
                            'has_options' => $qData['has_options'] ?? 0,
                            'marking_scheme' => $qData['marking_scheme'] ?? [],
                        ]
                    );

                    if (isset($qData['option'])) {
                        $optionIds = [];
                        foreach ($qData['option'] as $optData) {
                            $option = $question->option()->updateOrCreate(
                                ['id' => $optData['id'] ?? null],
                                [
                                    'text' => $optData['text'],
                                    'is_correct' => $optData['is_correct'] ?? 0,
                                    'order' => $optData['order'] ?? null
                                ]
                            );
                            $optionIds[] = $option->id;
                        }
                        $question->option()->whereNotIn('id', $optionIds)->delete();
                    }
                }
            }

            DB::commit();

            $updated = $this->repository->allData($id);
            return $this->successResponse($updated, 'ExamPaper updated successfully');
        } catch (\Throwable $e) {
            DB::rollBack();
            return $this->errorResponse('Failed to update ExamPaper: ' . $e->getMessage(), 500);
        }
    }
}
