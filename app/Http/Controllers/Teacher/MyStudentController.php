<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Http\Requests\Teacher\ShowStudentRequest;
use App\Http\Resources\StudentResource;
use App\Http\Resources\teacher\teachestudentdashboard;
use App\Models\StudentAssignment;
use App\Models\StudentAttamp;
use App\Models\User;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MyStudentController extends Controller
{
    use ApiResponseTrait;
    public function myStudent(Request $request)
    {
        $userId = 2;
        $limit = $request->query('limit', 10);
        $studentsQuery = User::query()
            ->select('users.id', 'users.username', 'users.email')
            ->join('student_assignments', 'student_assignments.student_id', '=', 'users.id')
            ->where('student_assignments.assigned_id', $userId)
            ->with([
                'grades:id,name',
                // 'subjects:id,name',
            ]);
        $students = $studentsQuery->paginate($limit);
        return $this->successResponsePaginate(StudentResource::collection($students));
    }

    //         student
    //  → subjects
    //    → topics
    //      → questions
    //        → answers
    //          → student_attempts

    public function showStudent(Request $request, $id)
    {
        $student = User::query()
            ->select('id', 'username', 'email')
            ->with([
                'grades:id,name',
                'subjects:id,name',
                'subjects.topics:id,name,subject_id',
                'subjects.topics.questions' => function ($q) use ($id) {
                    $q->select('id', 'topic_id')
                        ->with([
                            'answers' => function ($q) use ($id) {
                                $q->where('user_id', $id)
                                    ->select('id', 'question_id', 'attempt_id', 'user_id')
                                    ->with('attempt:id,score');
                            }
                        ]);
                },
            ])
            ->findOrFail($id);

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
}
