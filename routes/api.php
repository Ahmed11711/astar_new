<?php

use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\HelperForFront\FrontAuthController;
use App\Http\Controllers\Auth\CreateAccountController;
use App\Http\Middleware\CheckJwtToken;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;








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


Route::post('ai', function (Request $request) {

    Log::info('AI Request Data', [
        'data' => $request->all(),
        'ip' => $request->ip(),
        'headers' => $request->headers->all(),
    ]);

    return response()->json([
        'status' => 'ok'
    ]);
});

require __DIR__ . '/admin.php';
require __DIR__ . '/student.php';
require __DIR__ . '/teacher.php';
