<?php

namespace App\Http\Controllers\Student\MySubject;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

class MySubjectController extends Controller
{
    use ApiResponseTrait;
    public function index(Request $request)
    {
        $userId = $request->user_id;
        $mySubjectIds = $request->student_subject_ids;

        $subject = Subject::whereIn('id', $mySubjectIds)->get();
        return $this->successResponse($subject, 'My Subjects retrieved successfully');
    }
}
