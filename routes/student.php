<?php

use App\Http\Controllers\Student\PastPapersController;
use App\Http\Middleware\RoleToken;
use Illuminate\Support\Facades\Route;




Route::prefix('v1/student')->group(function () {

    Route::group([
        'middleware' => RoleToken::class,
        'roles' => ['student'],
    ], function () {




        Route::get('past-papers', [PastPapersController::class, 'index']);
        Route::get('past-papers/{examPaper}', [PastPapersController::class, 'show']);
    });
});
