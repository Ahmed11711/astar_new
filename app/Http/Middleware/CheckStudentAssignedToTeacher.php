<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class CheckStudentAssignedToTeacher
{

    public function handle(Request $request, Closure $next): Response
    {
        $teacgherId = $request->get('user_id');
        $studentId = $request->input('student_id');
        Log::alert("Checking assignment: Teacher ID {$teacgherId}, Student ID {$studentId}");
        return $next($request);
    }
}
