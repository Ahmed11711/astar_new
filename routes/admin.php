<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\CheckJwtTokenByAdmin;
use App\Http\Controllers\Admin\Auth\AuthController;
use App\Http\Controllers\Admin\User\UserController;


Route::post('admin/v1/login', [AuthController::class, 'login'])->name('admin.login');
Route::prefix('admin/v1')->middleware(CheckJwtTokenByAdmin::class)->group(function () {
    Route::apiResource('users', UserController::class)->names('user');
});

