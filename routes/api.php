<?php

use App\Http\Controllers\CustomerAuthenticationController;
use App\Http\Controllers\Vendor\ApplicationController;
use App\Http\Controllers\Vendor\CreditScoreController;
use App\Http\Controllers\Vendor\CustomerController;
use App\Http\Controllers\VendorAuthenticationController;
use App\Http\Controllers\VerificationController;
use App\Http\Middleware\PreventMultipleLogins;
use Illuminate\Support\Facades\Route;

Route::get('/process', [VerificationController::class, 'process']);

// Public Routes - Send OTP
Route::middleware(['throttle:60,60'])->group(function () {
    Route::post('customers/sendOtp', [CustomerAuthenticationController::class, 'sendOtp']);
    Route::post('customers/signup', [CustomerAuthenticationController::class, 'signup']);
    Route::post('vendors/sendOtp', [VendorAuthenticationController::class, 'sendOtp']);
    Route::post('vendors/signup', [VendorAuthenticationController::class, 'signup']);
});

Route::prefix('v1')->group(function (): void {
// Customer Authentication Routes
    Route::prefix('customers')->controller(CustomerAuthenticationController::class)->middleware(PreventMultipleLogins::class)->group(function () {
        Route::post('loginOtp', 'loginOtp');
        Route::post('loginPassword', 'loginPassword');
    });

// Vendor Authentication Routes
    Route::prefix('vendors')->controller(VendorAuthenticationController::class)->middleware(PreventMultipleLogins::class)->group(function () {
        Route::post('loginOtp', 'loginOtp');
        Route::post('loginPassword', 'loginPassword');
    });

// Authenticated User Routes
    Route::middleware('auth:sanctum')->group(function () {
        // Customer Profile
        Route::prefix('customers')->group(function () {
            Route::post('logout', [CustomerAuthenticationController::class, 'logout']);
        });

        // Vendor Profile
        Route::prefix('vendors')->group(function () {
            Route::post('logout', [VendorAuthenticationController::class, 'logout']);
        });

    });


    Route::prefix('vendors/{vendor:slug}')->scopeBindings()->group(function (): void {

            Route::withoutScopedBindings()->apiResource('customers', CustomerController::class)->names('vendors.customers')->only(['index', 'store', 'show']);

            Route::get('customers/{customer}/confirm', [VerificationController::class, 'confirm'])->name('vendors.customers.confirm');

            Route::post('customers/{customer}/credit-scores', [CreditScoreController::class, 'store'])->name('vendors.customers.credit-scores.store');
            Route::get('customers/{customer}/credit-scores', [CreditScoreController::class, 'show'])->name('vendors.customers.credit-scores.show');

            Route::get('applications', [ApplicationController::class, 'index'])->name('vendors.applications.index');
            Route::post('applications', [ApplicationController::class, 'store'])->name('vendors.applications.store');
            Route::get('applications/{application:id}/edit', [ApplicationController::class, 'edit'])->name('vendors.applications.edit');
            Route::match(['put', 'patch'], 'applications/{application:id}', [ApplicationController::class, 'update'])->name('vendors.applications.update');

        });
});