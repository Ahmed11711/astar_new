<?php

use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\HelperForFront\ApiHelperFrontController;
use App\Http\Controllers\Auth\CreateAccountController;
use App\Http\Middleware\CheckJwtToken;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;



Route::prefix('v1/')->group(function () {

 Route::prefix('auth/')->group(function () {
  Route::post('create-account', [CreateAccountController::class, 'createAccount']);
  Route::post('login', [LoginController::class, 'login']);
 });

 Route::prefix('global/')->group(function () {
  Route::get('grades', [ApiHelperFrontController::class, 'getGrades']);
  Route::get('all-school-teacher', [ApiHelperFrontController::class, 'allTeacherAndSchool']);
 });
});


// Route::get('run-migrate', function () {
//  Artisan::call('migrate', ['--force' => true]);

//  return response()->json([
//   'code' => Artisan::output()
//  ]);
// });

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
