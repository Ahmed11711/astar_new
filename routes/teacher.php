<?php

use App\Http\Controllers\Teacher\MyStudentController;
use App\Http\Middleware\RoleToken;
use Illuminate\Support\Facades\Route;



Route::prefix('v1/teacher')->group(function () {
    Route::group([
        'middleware' => RoleToken::class,
        'roles' => ['teacher'],
    ], function () {
        Route::get('my-students', [MyStudentController::class, 'myStudent']);

        Route::get('my-student/{student}', [MyStudentController::class, 'showStudent'])
            ->whereNumber('student');

        Route::get('my-student/exame/{student}', [MyStudentController::class, 'showStudentexam'])
            ->whereNumber('student');
    });
});
