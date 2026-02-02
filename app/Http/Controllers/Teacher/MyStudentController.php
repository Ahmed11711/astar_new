<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Http\Requests\Teacher\ShowStudentRequest;
use App\Http\Requests\Teacher\StudentQuizeRequest;
use App\Http\Resources\StudentResource;
use App\Http\Resources\teacher\teachestudentdashboard;
use App\Http\Resources\teacher\teachestudentexams;
use App\Http\Service\School\myTeacherService;
use App\Models\ExamPaper;
use App\Models\StudentAssignment;
use App\Models\StudentAttamp;
use App\Models\User;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MyStudentController extends Controller
{
    use ApiResponseTrait;
    public function __construct(public myTeacherService $myTeacherService) {}
    public function myStudent(Request $request)
    {
        $userId = $request->user_id;
        $role   = $request->role;
        $limit  = $request->query('limit', 10);

        $studentsQuery = User::query()
            ->select('users.id', 'users.username', 'users.email')
            ->join('student_assignments', 'student_assignments.student_id', '=', 'users.id')
            ->with([
                'grades:id,name',
            ]);

        if ($role === 'school') {

            $teacherIds = $this->myTeacherService->getMyTeachers($userId);

            $studentsQuery->whereIn('student_assignments.assigned_id', $teacherIds);
        } else {

            $studentsQuery->where('student_assignments.assigned_id', $userId);
        }

        $students = $studentsQuery->paginate($limit);

        return $this->successResponsePaginate(
            StudentResource::collection($students)
        );
    }



    public function showStudent(Request $request, $id)
    {
        $teacherId = $request->user_id;
        $student = User::query()->select('id', 'username', 'email')->with(
            [
                'grades:id,name',
                'subjects' => function ($q) use ($teacherId, $id) {
                    $q->select('subjects.id', 'subjects.name')->with(
                        [
                            'examPapers' => function ($q) use ($teacherId, $id) {
                                $q->where('created_by', $teacherId)->select('id', 'subject_id', 'title')
                                    ->with(['studentAttempts' => function ($q) use ($id) {
                                        $q->where('user_id', $id)->select('id', 'exam_id', 'user_id', 'score');
                                    }]);
                            },
                            'topics.questions.answers.attempt' => function ($q) use ($id) {
                                $q->where('user_id', $id);
                            },
                        ]
                    );
                }
            ]
        )->findOrFail($id);
        return new teachestudentdashboard($student);
    }


    public function showStudentexam(Request $request, $id) {}


    public function averageScorePerTopic($teacherId)
    {
        return StudentAttamp::query()
            ->join('answers', 'answers.attempt_id', '=', 'student_attempts.id')
            ->join('questions', 'questions.id', '=', 'answers.question_id')
            ->join('exam_papers', 'exam_papers.id', '=', 'student_attempts.exam_id')
            ->where('exam_papers.teacher_id', $teacherId)
            ->select(
                'questions.topic_id',
                DB::raw('AVG(student_attempts.score) as avg_score')
            )
            ->groupBy('questions.topic_id')
            ->get();
    }

    public function showStudentExams(Request $request, $studentId)
    {
        $teacherId = $request->userId;

        // --------------------------

        // --------------------------
        $student = User::query()
            ->select('id', 'username', 'email')
            ->with([
                'grades:id,name',

                'subjects' => function ($q) use ($teacherId, $studentId) {
                    $q->select('subjects.id', 'subjects.name') // لتجنب ambiguous
                        ->with([
                            // جلب كل الـ topics + questions + answers + attempts
                            'topics.questions.answers.attempt',

                            // Exam Papers الخاصة بالمدرس
                            'examPapers' => function ($q) use ($teacherId, $studentId) {
                                $q->where('created_by', $teacherId)
                                    ->select('exam_papers.id', 'exam_papers.subject_id', 'exam_papers.title')
                                    ->with([
                                        'studentAttempts' => function ($q) use ($studentId) {
                                            $q->where('user_id', $studentId)
                                                ->select('id', 'exam_id', 'user_id', 'time_remaining')
                                                ->with('answers');
                                        },
                                        'topics.questions' // لو حابب تفاصيل كل topic داخل الامتحان
                                    ]);
                            },
                        ]);
                },
            ])->findOrFail($studentId);

        // --------------------------
        // استدعاء Resource لحساب كل النتائج وجمع الـ scores
        // --------------------------
        return new teachestudentexams($student);
    }



    // from user


    public function getOneQuiez(StudentQuizeRequest $request)
    {;
        $id = $request->exam_id;
        $userId = $request->student_id;

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
        ])->find($id);


        return $examPaper;
    }







    // public function getOneMyQuiez(StudentQuizeRequest $request)
    // {
    //     $examId    = $request->exam_id;
    //     $studentId = $request->student_id;
    //     $teacherId = $request->user_id;

    //     // 1️⃣ Exam Paper (teacher ownership)
    //     $exam = ExamPaper::query()
    //         ->where('id', $examId)
    //         ->where('created_by', $teacherId)
    //         ->with([
    //             // 2️⃣ Questions
    //             'questions' => function ($q) use ($studentId) {
    //                 $q->select(
    //                     'id',
    //                     'exam_paper_id',
    //                     'question_string',
    //                     'question_number',
    //                     'question_max_score',
    //                     'marking_scheme',
    //                     'has_options'
    //                 )
    //                     ->with([
    //                         // 4️⃣ Answers (via attempt)
    //                         'answers' => function ($q) use ($studentId) {
    //                             $q->where('user_id', $studentId)
    //                                 ->select(
    //                                     'id',
    //                                     'question_id',
    //                                     'attempt_id',
    //                                     'response',
    //                                     // 'score'
    //                                 )
    //                                 ->with([
    //                                     // 3️⃣ Attempt
    //                                     'attempt:id,user_id,exam_id,score,time_taken'
    //                                 ]);
    //                         }
    //                     ]);
    //             }
    //         ])
    //         ->firstOrFail();

    //     return response()->json([
    //         'exam_id'   => $exam->id,
    //         'exam_name' => $exam->title,

    //         'questions' => $exam->questions->map(function ($question) {

    //             $answer = $question->answers->first();

    //             return [
    //                 'question_id'         => $question->id,
    //                 'question_text'       => $question->question_string,
    //                 'question_number'     => $question->question_number,
    //                 'question_max_score'  => $question->question_max_score,
    //                 'marking_scheme'      => $question->marking_scheme,
    //                 'has_options'         => $question->has_options,

    //                 'my_answer' => $answer ? [
    //                     'answer_id' => $answer->id,
    //                     'response'  => $answer->response,
    //                     'score'     => $answer->score,
    //                 ] : null,

    //                 'attempt' => $answer && $answer->attempt ? [
    //                     'attempt_id' => $answer->attempt->id,
    //                     'score'      => $answer->attempt->score,
    //                     'time_taken' => $answer->attempt->time_taken,
    //                 ] : null,
    //             ];
    //         }),
    //     ]);
    // }
}
