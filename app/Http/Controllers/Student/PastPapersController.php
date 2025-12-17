<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\ExamPaper;
use App\Models\paper;
use Illuminate\Http\Request;

class PastPapersController extends Controller
{

    public function index()
    {
        $papers = Paper::with('examPaper')->get();
        return $papers;
    }
}
