<?php

namespace App\Filament\Widgets;

use App\Models\Application;
use App\Models\Collateral;
use App\Models\CreditScore;
use App\Models\Customer;
use App\Models\Installment;
use App\Models\Vendor;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverviewWidget extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Customers', Customer::count())
                ->description('All registered customers')
                ->descriptionIcon('heroicon-o-user')
                ->color('success')
                ->chart([7, 3, 4, 5, 6, 3, 5, 3]),

            Stat::make('Total Vendors', Vendor::count())
                ->description('All registered vendors')
                ->descriptionIcon('heroicon-o-building-storefront')
                ->color('info')
                ->chart([2, 3, 4, 2, 5, 4, 3, 5]),

            Stat::make('Total Applications', Application::count())
                ->description('All applications')
                ->descriptionIcon('heroicon-o-document-text')
                ->color('warning')
                ->chart([3, 5, 7, 4, 8, 6, 9, 7]),

            Stat::make('Pending Applications', Application::where('status', 'terms-suggested')->count())
                ->description('Applications awaiting approval')
                ->descriptionIcon('heroicon-o-clock')
                ->color('danger'),

            Stat::make('Active Applications', Application::where('status', 'in-repayment')->count())
                ->description('Applications in repayment')
                ->descriptionIcon('heroicon-o-arrow-trending-up')
                ->color('primary'),

            Stat::make('Total Credit Scores', CreditScore::count())
                ->description('All credit scores issued')
                ->descriptionIcon('heroicon-o-chart-bar')
                ->color('success'),

            Stat::make('Total Collaterals', Collateral::count())
                ->description('All registered collaterals')
                ->descriptionIcon('heroicon-o-document-duplicate')
                ->color('info'),

            Stat::make('Overdue Installments', Installment::where('status', 'overdue')->count())
                ->description('Installments past due date')
                ->descriptionIcon('heroicon-o-exclamation-triangle')
                ->color('danger'),

            Stat::make('Paid Installments', Installment::where('status', 'paid')->count())
                ->description('Successfully paid installments')
                ->descriptionIcon('heroicon-o-check-circle')
                ->color('success'),
        ];
    }
}
