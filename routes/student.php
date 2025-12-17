<?php

use App\Http\Controllers\Student\PastPapersController;
use Illuminate\Support\Facades\Route;



Route::prefix('v1/student/')->group(function () {
    Route::get('past-papers', [PastPapersController::class, 'index']);

    // Route::get('attempt',)
});
