<?php

namespace App\Providers;

use App\Models\Application;
use App\Models\Collateral;
use App\Models\CreditScore;
use App\Models\Customer;
use App\Models\Installment;
use App\Policies\ApplicationPolicy;
use App\Policies\CollateralPolicy;
use App\Policies\CreditScorePolicy;
use App\Policies\CustomerPolicy;
use App\Policies\InstallmentPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Customer::class => CustomerPolicy::class,
        Application::class => ApplicationPolicy::class,
        CreditScore::class => CreditScorePolicy::class,
        Installment::class => InstallmentPolicy::class,
        Collateral::class => CollateralPolicy::class,
    ];

    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register policies
        foreach ($this->policies as $model => $policy) {
            Gate::policy($model, $policy);
        }
    }
}
