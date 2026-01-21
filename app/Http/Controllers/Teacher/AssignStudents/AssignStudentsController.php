<?php

namespace App\Http\Controllers\Teacher\AssignStudents;

use App\Http\Controllers\Controller;
use App\Http\Requests\Teacher\AssignStudentRequest;
use App\Repositories\StudentRegistrations\StudentRegistrationsRepositoryInterface;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

class AssignStudentsController extends Controller
{
    use ApiResponseTrait;
    public function __construct(public StudentRegistrationsRepositoryInterface $repository) {}

    public function index(Request $request)
    {
        $userId = $request->user_id;
        $role = $request->user_role;
        $allStudent = $this->repository->getByUserId($userId);
        return $this->successResponse($allStudent, 'all your student');
    }
    public function assignStudent(AssignStudentRequest $request)
    {
        $userId = $request->user_id;
        $role = $request->user_role;
        $email  = $request->email;

        $data = [
            'email' => $email,
            'affiliation_type' => $role,
            'user_id' => $userId,
        ];
        $created = $this->repository->create($data);
        return $this->successResponse($created, "create SuccessFull");
    }

    public function removeassignStudent(Request $request)
    {
        $userId = $request->user_id;
        $email  = $request->email;

        if (!$email) {
            return $this->errorResponse('Email is required');
        }

        $student = $this->repository->findBYKey('email', $email);

        if (!$student) {
            return $this->errorResponse('Student not found');
        }

        if ($student->user_id != $userId) {
            return $this->errorResponse('Unauthorized action');
        }

        $student->delete();


        return $this->successResponse('Student delete successfully');
    }
}
