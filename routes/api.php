<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\CheckJwtToken;
use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\Company\CompanyCOntroller;
use App\Http\Controllers\Api\Withdraw\WithdrawController;
use App\Http\Controllers\Api\Affiliate\AffiliateController;
use App\Http\Controllers\Api\Notifications\NotificationsController;

Route::prefix('v1/')->group(function () {
  Route::prefix('auth/')->group(function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', [AuthController::class, 'register']);
    Route::post('verify-email', [AuthController::class, 'verifyEmail']);
    Route::post('verify-affiliate', [AuthController::class, 'verifyAffiliate']);
    Route::post('resend-otp', [AuthController::class, 'resendOtp']);
  
    Route::middleware(CheckJwtToken::class)->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('me', [AuthController::class, 'me']);
    Route::post('update-profile', [AuthController::class, 'updateProfile']);

    });
   });

       Route::middleware(CheckJwtToken::class)->group(function () {
                Route::get('withdraw', [WithdrawController::class, 'index']);

       // without auth routes
        Route::get('my-affiliate', [AffiliateController::class, 'myAffiliate']);
        Route::get('notification',[NotificationsController::class,'index']);

        Route::post('withdraw', [WithdrawController::class, 'Withdraw']);
        Route::post('add-balance', [WithdrawController::class, 'addBalance']);
        Route::get('companies',[CompanyCOntroller::class,'index']);
        Route::get('settings',[CompanyCOntroller::class,'setting']);
   });
});

require __DIR__.'/admin.php';