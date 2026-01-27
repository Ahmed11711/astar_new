<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class CheckStudentAssignedToTeacher
{

    public function handle(Request $request, Closure $next): Response
    {
        $teacgherId = $request->get('user_id');
        $role = $request->get('user_role');
        $studentId = $request->input('student_id');

        if ($role == 'teacher') {
            $isAssigned = DB::table('student_assignments')
                ->where('assigned_id', $teacgherId)
                ->where('student_id', $studentId)
                ->exists();

            if (!$isAssigned) {
                return response()->json([
                    'success' => false,
                    'message' => 'You are not assigned to this student.'
                ], 403);
            }
        }

        return $next($request);
    }
}
