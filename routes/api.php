<?php

use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\HelperForFront\FrontAuthController;
use App\Http\Controllers\Auth\CreateAccountController;
use App\Http\Middleware\CheckJwtToken;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;




Route::get('run-migrate', function () {
    //
    Artisan::call('migrate:fresh', ['--force' => true]);
    Artisan::call('db:seed', [
        '--force' => true,
    ]);

    return response()->json([
        'code' => Artisan::output()
    ]);
});
Route::get('delete-student-attempts', function () {
    DB::table('student_attempts')->truncate();

    return response()->json([
        'message' => 'All student attempts deleted successfully.'
    ]);
});

Route::prefix('v1/')->group(function () {

    Route::prefix('auth/')->group(function () {
        Route::post('create-account', [CreateAccountController::class, 'createAccount']);
        Route::post('login', [LoginController::class, 'login']);
        Route::get('me', [LoginController::class, 'me'])->middleware(CheckJwtToken::class);
    });

    Route::prefix('global/')->group(function () {
        Route::get('grades', [FrontAuthController::class, 'getGrades']);
        Route::get('all-school-teacher', [FrontAuthController::class, 'allTeacherAndSchool']);
        Route::get('packages', [FrontAuthController::class, 'getPackageByAccount']);
    });
});


Route::get('run-migrate', function () {
    //
    Artisan::call('migrate', ['--force' => true]);

    return response()->json([
        'code' => Artisan::output()
    ]);
});
Route::get('/generate-jwt-secret', function () {
    // Artisan::call('jwt:secret');
    Artisan::call('jwt:secret', ['--force' => true]);


    return response()->json([
        'status' => 'success',
        'output' => Artisan::output(),
    ]);
});

// Route::get('run-migrate-refresh', function () {
//  // Artisan::call('migrate:refresh', [
//  //  '--force' => true,
//  // ]);

//  Artisan::call('db:seed', [
//   '--force' => true,
//  ]);

//  return response()->json([
//   'output' => Artisan::output(),
//  ]);
// });
require __DIR__ . '/admin.php';
require __DIR__ . '/student.php';
