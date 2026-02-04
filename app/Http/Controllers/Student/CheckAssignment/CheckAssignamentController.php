<?php

namespace App\Http\Controllers\Student\CheckAssignment;

use App\Http\Controllers\Controller;
use App\Http\Requests\Student\CheckAssignment\CheckAssignmentRequest;
use App\Models\StudentRegistrations;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

class CheckAssignamentController extends Controller
{
    use ApiResponseTrait;

    public function index(CheckAssignmentRequest $request)
    {
        $validatedData = $request->validated();
        $studentResign = StudentRegistrations::where('email', $validatedData['email'])
            ->where('user_id', $validatedData['assignment_id'])
            ->first();

        // if false return respponse error
        if (!$studentResign) {
            return $this->errorResponse(
                'Student not registered for this assignment',
                404
            );
        }

        return $this->successResponse([
            'is_registered' => $studentResign ? true : false,
        ], 'Check completed successfully');
    }
}
