<?php

use App\Http\Controllers\Teacher\AssignStudents\AssignStudentsController;
use App\Http\Controllers\Teacher\bugs\bugsController;
use App\Http\Controllers\Teacher\MyExame\MyExameTeacherController;
use App\Http\Controllers\Teacher\MyStudentController;
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


        Route::get('assign-student', [AssignStudentsController::class, 'index']);
        Route::post('assign-student', [AssignStudentsController::class, 'assignStudent']);
        Route::post('remove-assign-student', [AssignStudentsController::class, 'removeassignStudent']);
        Route::get('my-bugs', [bugsController::class, 'index']);
        Route::post('my-bugs', [bugsController::class, 'store']);
    });
});
