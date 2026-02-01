<?php

use App\Http\Controllers\School\AllStudent\MyStudentController;
use App\Http\Middleware\RoleToken;
use Illuminate\Support\Facades\Route;












Route::prefix('v1/school')->group(function () {
    Route::group([
        'middleware' => RoleToken::class,
        'roles' => ['teacher', 'school'],
    ], function () {

        Route::get('my-students', [MyStudentController::class, 'index']);
    });
});
