<?php

namespace App\Repositories\ExamPaper;

use App\Repositories\BaseRepository\BaseRepositoryInterface;

interface ExamPaperRepositoryInterface extends BaseRepositoryInterface
{
 public function allData($id);
}
