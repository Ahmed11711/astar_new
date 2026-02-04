<?php

use App\Http\Controllers\otpcontroller;
use App\Http\Controllers\Student\Ai\AiChateController;
use App\Http\Controllers\Student\AnswerController;
use App\Http\Controllers\Student\AttmpateWithAnswerController;
use App\Http\Controllers\Student\CheckAssignment\CheckAssignamentController;
use App\Http\Controllers\Student\Dashboard\DashboardController;
use App\Http\Controllers\Student\FeadbackAnswer\FeadBackAnswerController;
use App\Http\Controllers\Student\MySubject\MySubjectController;
use App\Http\Controllers\Student\Package\PakageController;
use App\Http\Controllers\Student\PastPapersController;
use App\Http\Controllers\Student\TopicWise\TopicWiseController;
use App\Http\Middleware\CheckFeatureLimit;
use App\Http\Middleware\RoleToken;
use Illuminate\Support\Facades\Route;








Route::prefix('v1/student')->group(function () {

    Route::post('verfiy-email', [CheckAssignamentController::class, 'index']);


    Route::group([
        'middleware' => RoleToken::class,
        'roles' => ['student'],
    ], function () {

        Route::apiResource('chat-ai', AiChateController::class);
        Route::get("my-package", [PakageController::class, 'getPackageByAccount']);
        Route::post("upgrade-my-package", [PakageController::class, 'upgrade']);
        Route::get('dashboard', [DashboardController::class, 'index']);
        Route::get('dashboard-paper-scores', [DashboardController::class, 'PaperScores']);
        Route::get('dashboard-chart', [DashboardController::class, 'topicProgressPerDay']);

        Route::get('past-papers', [PastPapersController::class, 'index']);
        Route::get('past-paper/{examPaper}', [PastPapersController::class, 'show']);
        // Route::get('past-paper/{examPaper}', [PastPapersController::class, 'show'])->middleware(CheckFeatureLimit::class . ':attampted');
        Route::get('past-paper/attempt/{attemptId}', [PastPapersController::class, 'showByAttempt']);

        Route::post('attamepate', [AttmpateWithAnswerController::class, 'createAttamepate']);
        Route::get('attamepate', [AttmpateWithAnswerController::class, 'index']);
        Route::post('answers', [AnswerController::class, 'saveAnswersOptimized']);
        Route::post('asnwers-topicwise', [AnswerController::class, 'saveAnswersAutoAttempt']);

        Route::post('topicwise', [TopicWiseController::class, 'index']);
        // My Subject
        Route::get('my-subjects', [MySubjectController::class, 'index']);

        // Feadback Answer
        Route::post('feadback-answer', [FeadBackAnswerController::class, 'index']);
    });
});
