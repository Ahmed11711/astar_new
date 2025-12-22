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
        $role   = $request->user_role;



        $gradeId    = $request->student_grade_id;
        $subjectIds = $request->student_subject_ids;




        $papers = ExamPaper::query()
            ->where('grade_id', $gradeId)
            ->whereIn('subject_id', $subjectIds)
            ->with([
                'subject:id,name',
                'studentAttempt' => function ($query) use ($userId) {
                    $query->where('user_id', $userId);
                }
            ])
            ->get();

        return response()->json([
            'success' => true,
            'data'    => $papers
        ]);
    }




    public function show(Request $request, $id)
    {
        $userId = $request->user_id;

        $examPaper = ExamPaper::with([
            'questions.options',
            'questions.audios',
            'questions.images',
            'studentAttempt' => function ($query) use ($userId) {
                $query->where('user_id', $userId);
            },
            'questions.asnwer' => function ($query) use ($userId) {
                $query->where('user_id', $userId);
            },
        ])->findOrFail($id);

        return $examPaper;
    }
}
