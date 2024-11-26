<?php

use App\Http\Controllers\Api\V1\AppVersionController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::prefix('app')->group(function () {
        Route::get('/check-version', [AppVersionController::class, 'checkVersion']);
    });
});
