<?php

use App\Http\Controllers\otpcontroller;
use App\Http\Controllers\Student\Ai\AiChateController;
use App\Http\Controllers\Student\AnswerController;
use App\Http\Controllers\Student\AttmpateWithAnswerController;
use App\Http\Controllers\Student\Dashboard\DashboardController;
use App\Http\Controllers\Student\Package\PakageController;
use App\Http\Controllers\Student\PastPapersController;
use App\Http\Middleware\RoleToken;
use Illuminate\Support\Facades\Route;








Route::prefix('v1/student')->group(function () {

    Route::get('otp', [otpcontroller::class, 'sendOtp']);

    Route::group([
        'middleware' => RoleToken::class,
        'roles' => ['student'],
    ], function () {



        Route::apiResource('chat-ai', AiChateController::class);
        Route::get("my-package", [PakageController::class, 'getPackageByAccount']);
        Route::get("upgrade-my-package", [PakageController::class, 'upgrade']);
        Route::get('dashboard', [DashboardController::class, 'index']);
        Route::get('past-papers', [PastPapersController::class, 'index']);
        Route::get('past-paper/{examPaper}', [PastPapersController::class, 'show']);
        Route::post('attamepate', [AttmpateWithAnswerController::class, 'createAttamepate']);
        Route::get('attamepate', [AttmpateWithAnswerController::class, 'index']);
        Route::post('answers', [AnswerController::class, 'saveAnswersOptimized']);
    });
});
