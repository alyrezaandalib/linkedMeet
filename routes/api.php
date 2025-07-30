<?php

use App\Http\Controllers\Api\V1\App\CompanyActivityTypeController;
use App\Http\Controllers\Api\V1\App\IndustryController;
use App\Http\Controllers\Api\V1\App\JobTitleController;
use App\Http\Controllers\Api\V1\App\VersionController;
use App\Http\Controllers\Api\V1\Auth\EmailVerificationController;
use App\Http\Controllers\Api\V1\Auth\LinkedInController;
use App\Http\Controllers\Api\V1\Auth\LoginController;
use App\Http\Controllers\Api\V1\Auth\LogoutController;
use App\Http\Controllers\Api\V1\Auth\PasswordController;
use App\Http\Controllers\Api\V1\Auth\RegisterController;
use App\Http\Controllers\Api\V1\Auth\ResendVerificationCodeController;
use App\Http\Controllers\Api\V1\Auth\ForgotPasswordController;
use App\Http\Controllers\Api\V1\Chat\SendMessageController;
use App\Http\Controllers\Api\V1\User\AvatarController;
use App\Http\Controllers\Api\V1\User\ChatController;
use App\Http\Controllers\Api\V1\User\InformationController;
use App\Http\Controllers\Api\V1\User\LocationController;
use App\Http\Controllers\Api\V1\User\NearbyUsersController;
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
        Route::post('forgot-password', [ForgotPasswordController::class, 'sendCode']);
        Route::post('reset-forgotten-password', [ForgotPasswordController::class, 'reset']);

        Route::get('linkedin', [LinkedInController::class, 'redirectToLinkedIn']);
        Route::get('linkedin/callback', [LinkedInController::class, 'handleLinkedInCallback']);


        Route::middleware('auth:sanctum')->group(function () {
            Route::post('logout', [LogoutController::class, 'logout']);
            Route::patch('change-password', [PasswordController::class, 'change']);
        });

    });

    // User
    Route::middleware('auth:sanctum')->prefix('user')->group(function () {

        Route::get('profile', [ProfileController::class, 'show']);
        Route::patch('profile', [ProfileController::class, 'update']);
        Route::patch('name', [ProfileController::class, 'updateName']);
        Route::post('avatar', [AvatarController::class, 'upload']);
        Route::get('{id}/profile', [ProfileController::class, 'showUserProfile']);

        Route::post('location', [LocationController::class, 'update']);
        Route::get('nearby-users', [NearbyUsersController::class, 'index']);
        Route::get('chats', [ChatController::class, 'index']);

        Route::get('company-activity-types', [UserCompanyActivityTypeController::class, 'index']);
        Route::put('company-activity-types', [UserCompanyActivityTypeController::class, 'store']);
        Route::patch('industry', [\App\Http\Controllers\Api\V1\User\IndustryController::class, 'update']);
        Route::patch('job-title', [\App\Http\Controllers\Api\V1\User\JobTitleController::class, 'update']);
        Route::patch('information', [InformationController::class, 'update']);

    });

    // Chat
    Route::middleware('auth:sanctum')->prefix('chat')->group(function () {

        Route::post('send-message', [SendMessageController::class, 'sendMessage']);

    });
});
