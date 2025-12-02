<?php

use Termwind\Components\Raw;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\CheckJwtToken;
use App\Http\Controllers\Api\Auth\AuthController;

Route::prefix('v1/')->group(function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', [AuthController::class, 'register']);
    Route::post('verify-email', [AuthController::class, 'verifyEmail']);
    Route::post('verify-affiliate', [AuthController::class, 'verifyAffiliate']);
    // Route::post('resend-otp', [AuthController::class, 'resendOtp']);
   


    Route::middleware(CheckJwtToken::class)->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('me', [AuthController::class, 'me']);
    });
});