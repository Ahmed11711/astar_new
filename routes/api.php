<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\CheckJwtToken;
use Illuminate\Support\Facades\Artisan;


Route::prefix('v1/')->group(function () {

  Route::get('run-migrate', function () {
    Artisan::call('migrate', ['--force' => true]);

    return response()->json([
      'code' => Artisan::output()
    ]);
  });
});

require __DIR__ . '/admin.php';
