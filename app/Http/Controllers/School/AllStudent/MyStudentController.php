<?php

namespace App\Http\Controllers\School\AllStudent;

use \App\Traits\ApiResponseTrait;
use App\Http\Controllers\Controller;
use App\Http\Resources\StudentResource;
use App\Models\StudentAssignment;
use App\Models\User;
use Illuminate\Http\Request;

class MyStudentController extends Controller
{
    use ApiResponseTrait;
    public function index(Request $request)
    {
        $userId = $request->user_id;
        $limit = $request->query('limit', 10);

        $teachers = StudentAssignment::where('assigned_id', $userId)
            ->pluck('student_id')
            ->toArray();

        $students = User::query()
            ->select('users.id', 'users.username', 'users.email')
            ->whereIn('users.id', $teachers)
            ->with([
                'grades:id,name',
            ])
            ->paginate($limit);

        return $this->successResponsePaginate(
            StudentResource::collection($students)
        );
    }
}
