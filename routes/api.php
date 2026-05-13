<?php

use App\Http\Controllers\Api\AuthOtpController;
use App\Http\Controllers\Api\SettingsApiController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthOtpController::class, 'register']);
    Route::post('/signup', [AuthOtpController::class, 'signup']);
    Route::post('/verify-register-otp', [AuthOtpController::class, 'verifyRegisterOtp']);

    Route::post('/login', [AuthOtpController::class, 'login']);
    Route::post('/login-password', [AuthOtpController::class, 'loginPassword']);
    Route::post('/verify-login-otp', [AuthOtpController::class, 'verifyLoginOtp']);

    Route::post('/forgot-password/request-otp', [AuthOtpController::class, 'requestPasswordResetOtp']);
    Route::post('/forget-password', [AuthOtpController::class, 'forgetPassword']);
    Route::post('/check-otp', [AuthOtpController::class, 'checkOtp']);
    Route::post('/forgot-password/reset', [AuthOtpController::class, 'resetPasswordWithOtp']);
    Route::post('/reset-password', [AuthOtpController::class, 'resetPassword']);

    Route::post('/resend-otp', [AuthOtpController::class, 'resendOtp']);

    Route::middleware('auth:api')->group(function () {
        Route::get('/me', [AuthOtpController::class, 'me']);
        Route::post('/refresh', [AuthOtpController::class, 'refresh']);
        Route::post('/logout', [AuthOtpController::class, 'logout']);

        Route::post('/change-password', [AuthOtpController::class, 'changePassword']);
        Route::delete('/delete-account', [AuthOtpController::class, 'deleteAccount']);
        Route::post('/switch-account', [AuthOtpController::class, 'switchAccount']);

        Route::get('/profile', [AuthOtpController::class, 'userProfileGet']);
        Route::put('/profile', [AuthOtpController::class, 'userProfileUpdate']);
        Route::post('/profile-image', [AuthOtpController::class, 'profileImageUpdate']);
        Route::get('/profile-image', [AuthOtpController::class, 'userProfileImage']);

        Route::post('/fcm-token', [AuthOtpController::class, 'fcmToken']);
    });
});

Route::middleware(['auth:api', 'permission:manage settings'])->prefix('settings')->group(function () {
    Route::get('/', [SettingsApiController::class, 'index']);
    Route::put('/{group}', [SettingsApiController::class, 'update']);
});
