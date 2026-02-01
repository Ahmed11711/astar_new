<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\ExamPaper;
use App\Models\paper;
use App\Models\StudentAttamp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PastPapersController extends Controller
{

    public function index(Request $request)
    {
        $userId = $request->user_id;
        $role   = $request->user_role;
        $gradeId = $request->student_grade_id;
        $subjectIds = $request->student_subject_ids;
        $student_assignment_id = $request->student_assignment_id;

        $papers = ExamPaper::query()
            ->where('grade_id', $gradeId)
            ->whereIn('subject_id', $subjectIds)
            ->where(function ($query) use ($student_assignment_id) {
                $query->whereNull('created_by')
                    ->orWhere('created_by', $student_assignment_id);
            })
            ->with([
                'paper:id,name',
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
            'questions.lastAnswer' => function ($query) use ($userId) {
                $query->where('user_id', $userId);
            },
        ])->findOrFail($id);

        return $examPaper;
    }

    public function showByAttempt(Request $request, $attemptId)
    {

        $userId = $request->user_id;


        // Load Attempt + ExamPaper + Questions + Options/Media + Student Answers
        $attempt = StudentAttamp::where('user_id', $userId)->with([
            'examPaper.questions.options',
            'examPaper.questions.audios',
            'examPaper.questions.images',
            'examPaper.questions.lastAnswer' => function ($query) use ($userId) {
                $query->where('user_id', $userId);
            },
        ])->findOrFail($attemptId);

        return response()->json($attempt);
    }
}
