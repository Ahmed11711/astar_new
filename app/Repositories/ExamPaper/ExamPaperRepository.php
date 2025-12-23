<?php

namespace App\Repositories\ExamPaper;

use App\Repositories\ExamPaper\ExamPaperRepositoryInterface;
use App\Repositories\BaseRepository\BaseRepository;
use App\Models\ExamPaper;

class ExamPaperRepository extends BaseRepository implements ExamPaperRepositoryInterface
{
    public function __construct(ExamPaper $model)
    {
        parent::__construct($model);
    }

    public function allData($id)
    {
        $data = $this->model
            ->with([
                'subject',
                'grade',
                'paper',
                'questions.options',
                'questions.audios',
                'questions.images',
            ])
            ->find($id);


        if (!$data) {
            return null;
        }

        return $data;
    }
}
