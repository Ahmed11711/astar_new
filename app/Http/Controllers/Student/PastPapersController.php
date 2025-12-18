<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\ExamPaper;
use App\Models\paper;
use Illuminate\Http\Request;

class PastPapersController extends Controller
{

    public function index(Request $request)
    {
        $userId = $request->user_id;
        $userName = $request->user_grade;
        $papers = paper::with('examPaper')->get();
        return $papers;
    }

    public function show(Request $request, $id)
    {
        $userId = $request->user_id;
        $question_id = $id;

        $papers = ExamPaper::where('id', $question_id)->with('questions')->get();

        return $papers;
    }
}
