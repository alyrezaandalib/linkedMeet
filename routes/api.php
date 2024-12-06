<?php

use App\Http\Controllers\Api\V1\App\CompanyActivityTypeController;
use App\Http\Controllers\Api\V1\App\IndustryController;
use App\Http\Controllers\Api\V1\App\JobTitleController;
use App\Http\Controllers\Api\V1\App\VersionController;
use App\Http\Controllers\Api\V1\Auth\EmailVerificationController;
use App\Http\Controllers\Api\V1\Auth\LinkedInController;
use App\Http\Controllers\Api\V1\Auth\LoginController;
use App\Http\Controllers\Api\V1\Auth\RegisterController;
use App\Http\Controllers\Api\V1\Auth\ResendVerificationCodeController;
use App\Http\Controllers\Api\V1\User\ProfileController;
use App\Http\Controllers\Api\V1\User\UserCompanyActivityTypeController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {

    // App
    Route::prefix('app')->group(function () {

        Route::get('check-version', [VersionController::class, 'checkVersion']);

        Route::middleware('auth:sanctum')->group(function () {
            Route::get('company-activity-types', [CompanyActivityTypeController::class, 'index']);
            Route::get('industries', [IndustryController::class, 'index']);
            Route::get('job-titles', [JobTitleController::class, 'index']);
        });

    });

    // Auth
    Route::prefix('auth')->group(function () {

        Route::post('register', [RegisterController::class, 'register']);
        Route::post('login', [LoginController::class, 'login']);
        Route::post('verify-email', [EmailVerificationController::class, 'verifyEmail']);
        Route::post('resend-verification-code', [ResendVerificationCodeController::class, 'resendVerificationCode']);

        Route::get('linkedin', [LinkedInController::class, 'redirectToLinkedIn']);
        Route::get('linkedin/callback', [LinkedInController::class, 'handleLinkedInCallback']);

    });

    // User
    Route::middleware('auth:sanctum')->prefix('user')->group(function () {

        Route::get('profile', [ProfileController::class, 'show']);

        Route::get('company-activity-types', [UserCompanyActivityTypeController::class, 'index']);
        Route::post('company-activity-types', [UserCompanyActivityTypeController::class, 'store']);
        Route::patch('industry', [\App\Http\Controllers\Api\V1\User\IndustryController::class, 'update']);
        Route::patch('job-title', [\App\Http\Controllers\Api\V1\User\JobTitleController::class, 'update']);

    });

});
