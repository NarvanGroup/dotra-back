<?php

use App\Http\Controllers\Vendor\ApplicationController;
use App\Http\Controllers\Vendor\CreditScoreController;
use App\Http\Controllers\Vendor\CustomerController;
use Illuminate\Support\Facades\Route;


Route::prefix('v1')->group(function (): void {
    Route::prefix('vendors/{vendor:slug}')
        ->scopeBindings()
        ->group(function (): void {

            Route::withoutScopedBindings()
                ->apiResource('customers', CustomerController::class)
                ->names('vendors.customers')
                ->only(['index', 'store', 'show']);

            Route::post('customers/{customer}/credit-scores', [CreditScoreController::class, 'store'])
                ->name('vendors.customers.credit-scores.store');
            Route::get('customers/{customer}/credit-scores', [CreditScoreController::class, 'show'])
                ->name('vendors.customers.credit-scores.show');

            Route::get('applications', [ApplicationController::class, 'index'])->name('vendors.applications.index');
            Route::post('applications', [ApplicationController::class, 'store'])->name('vendors.applications.store');
            Route::get('applications/{application:id}/edit', [ApplicationController::class, 'edit'])->name('vendors.applications.edit');
            Route::match(['put', 'patch'], 'applications/{application:id}', [ApplicationController::class, 'update'])
                ->name('vendors.applications.update');

        });
});


