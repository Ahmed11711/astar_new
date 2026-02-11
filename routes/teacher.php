<?php

use App\Http\Controllers\School\AllTeacher\AllTeacherController;
use App\Http\Controllers\School\AllTeacher\ReportOfTeacherController;
use App\Http\Controllers\Student\Ai\AnswerAiExameController;
use App\Http\Controllers\Student\MySubject\MySubjectController;
use App\Http\Controllers\Teacher\AssignStudents\AssignStudentsController;
use App\Http\Controllers\Teacher\bugs\bugsController;
use App\Http\Controllers\Teacher\bugs\ReportTeacherController;
use App\Http\Controllers\Teacher\Dashboard\DashboardTeacherController;
use App\Http\Controllers\Teacher\MyExame\MyExameTeacherController;
use App\Http\Controllers\Teacher\MyStudentController;
use App\Http\Controllers\Teacher\StudentChatAi\StudentChateAiController;
use App\Http\Middleware\CheckStudentAssignedToTeacher;
use App\Http\Middleware\RoleToken;
use Illuminate\Support\Facades\Route;






Route::prefix('v1/teacher')->group(function () {
    Route::group([
        'middleware' => RoleToken::class,
        'roles' => ['teacher', 'school'],
    ], function () {
        Route::get('my-students', [MyStudentController::class, 'myStudent']);
        Route::get('my-student/{student}', [MyStudentController::class, 'showStudent'])
            ->whereNumber('student');
        Route::get('my-student/exame/{student}', [MyStudentController::class, 'showStudentexam'])
            ->whereNumber('student');
        Route::get('showStudentExams/{studentId}', [MyStudentController::class, 'showStudentExams']);
        Route::post('getOneQuiez', [MyStudentController::class, 'getOneQuiez']);
        Route::get('getAllQuizzesStatistics', [MyExameTeacherController::class, 'teacherExamsDashboard']);

        Route::get('dashboard', [DashboardTeacherController::class, 'index']);
        // My Subject
        Route::get('my-subjects', [MySubjectController::class, 'subjectWithPaper']);


        // assign 
        Route::get('assign-student', [AssignStudentsController::class, 'index']);
        Route::post('assign-student', [AssignStudentsController::class, 'assignStudent']);
        Route::post('remove-assign-student', [AssignStudentsController::class, 'removeassignStudent']);

        // bugs and reports
        Route::get('my-bugs', [bugsController::class, 'index']);
        Route::post('my-bugs', [bugsController::class, 'store']);
        Route::get('my-reports', [ReportTeacherController::class, 'index']);
        Route::post('my-reports', [ReportTeacherController::class, 'store']);
        // ... other teacher routes for Ai ...
        // get chat ai for teacher
        Route::post('student-all-chat-ai', [StudentChateAiController::class, 'index']);
        Route::post('student-one-chat-ai', [StudentChateAiController::class, 'show']);
        Route::post('student-update-one-chat-ai', [StudentChateAiController::class, 'update']);

        // update anser with feedback
        Route::post('ai', [AnswerAiExameController::class, 'handleAi']);
        Route::post('teacher-feedback', [AnswerAiExameController::class, 'handelTeacherFeedback'])->middleware(CheckStudentAssignedToTeacher::class);
    });
});

Route::prefix('v1/school')->group(function () {
    Route::group([
        'middleware' => RoleToken::class,
        'roles' => ['school'],
    ], function () {
        Route::get('all-my-teacher', [AllTeacherController::class, 'allTeachers']);
        Route::get('dashboard-teacher', [AllTeacherController::class, 'Dashboard']);
        Route::get('reports-of-teacher', [ReportOfTeacherController::class, 'index']);
        Route::put('reports-of-teacher/{id}', [ReportOfTeacherController::class, 'update'])->whereNumber('id');
    });
});
