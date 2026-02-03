<?php

use App\Http\Controllers\Admin\HeroSection\HeroSectionController;
use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\HelperForFront\FrontAuthController;
use App\Http\Controllers\Auth\CreateAccountController;
use App\Http\Controllers\Student\Ai\AnswerAiExameController;
use App\Http\Middleware\CheckJwtToken;
use App\Mail\SentOtpMail;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;












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

    Route::prefix('front/')->group(function () {
        Route::get('hero-sections', [HeroSectionController::class, 'index']);
    });
});

Route::post('ai', [AnswerAiExameController::class, 'handelAiFeadback']);



require __DIR__ . '/admin.php';
require __DIR__ . '/student.php';
require __DIR__ . '/teacher.php';
require __DIR__ . '/school.php';
