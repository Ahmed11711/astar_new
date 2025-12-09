<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\CheckJwtToken;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\Api\HelperForFront\ApiHelperFrontController;


Route::prefix('v1/')->group(function () {

 Route::get('run-migrate', function () {
  Artisan::call('migrate', ['--force' => true]);

  return response()->json([
   'code' => Artisan::output()
  ]);
 });

 Route::get('run-migrate-refresh', function () {
  Artisan::call('migrate:refresh', [
   '--force' => true,
  ]);

  return response()->json([
   'output' => Artisan::output(),
  ]);
 });

 Route::get('test', function () {
  return 555;
 });

 Route::prefix('global/')->group(function () {
  Route::get('grades', [ApiHelperFrontController::class, 'getGrades']);
  Route::get('all-school-teacher', [ApiHelperFrontController::class, 'allTeacherAndSchool']);
 });
});

require __DIR__ . '/admin.php';
