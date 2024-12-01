<?php

use App\Http\Controllers\Api\V1\AppVersionController;
use App\Http\Controllers\Api\V1\Auth\EmailVerificationController;
use App\Http\Controllers\Api\V1\Auth\RegisterController;
use App\Http\Controllers\Api\V1\Auth\ResendVerificationCodeController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::prefix('app')->group(function () {
        Route::get('/check-version', [AppVersionController::class, 'checkVersion']);
    });

    Route::prefix('auth')->group(function () {
        Route::post('register', [RegisterController::class, 'register']);
        Route::post('verify-email', [EmailVerificationController::class, 'verifyEmail']);
        Route::post('resend-verification-code', [ResendVerificationCodeController::class, 'resendVerificationCode']);
    });

});
