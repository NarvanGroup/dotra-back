<?php

use App\Http\Controllers\CustomerAuthenticationController;
use App\Http\Controllers\VendorAuthenticationController;
use App\Http\Controllers\VerificationController;
use App\Http\Middleware\PreventMultipleLogins;
use Illuminate\Support\Facades\Route;

Route::get('/confirm', [VerificationController::class, 'confirm']);
Route::get('/process', [VerificationController::class, 'process']);

// Public Routes - Send OTP
Route::middleware(['throttle:60,60'])->group(function () {
    Route::post('customer/sendOtp', [CustomerAuthenticationController::class, 'sendOtp']);
    Route::post('customer/signup', [CustomerAuthenticationController::class, 'signup']);
    Route::post('vendor/sendOtp', [VendorAuthenticationController::class, 'sendOtp']);
    Route::post('vendor/signup', [VendorAuthenticationController::class, 'signup']);
});

// Customer Authentication Routes
Route::prefix('customer')->controller(CustomerAuthenticationController::class)->middleware(PreventMultipleLogins::class)->group(function () {
    Route::post('loginOtp', 'loginOtp');
    Route::post('loginPassword', 'loginPassword');
});

// Vendor Authentication Routes
Route::prefix('vendor')->controller(VendorAuthenticationController::class)->middleware(PreventMultipleLogins::class)->group(function () {
    Route::post('loginOtp', 'loginOtp');
    Route::post('loginPassword', 'loginPassword');
});

// Authenticated User Routes
Route::middleware('auth:sanctum')->group(function () {
    // Customer Profile
    Route::prefix('customer')->group(function () {
        Route::post('logout', [CustomerAuthenticationController::class, 'logout']);
    });

    // Vendor Profile
    Route::prefix('vendor')->group(function () {
        Route::post('logout', [VendorAuthenticationController::class, 'logout']);
    });

});