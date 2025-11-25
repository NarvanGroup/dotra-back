<?php

namespace App\Filament\Widgets;

use App\Models\Installment;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class MonthlyRevenueWidget extends ChartWidget
{
    protected ?string $heading = 'Monthly Revenue (Paid Installments)';

    protected static ?int $sort = 7;

    protected function getData(): array
    {
        $data = [];
        $labels = [];

        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $labels[] = $date->format('M Y');

            $revenue = Installment::where('status', 'paid')
                ->whereYear('paid_at', $date->year)
                ->whereMonth('paid_at', $date->month)
                ->sum('amount');

            // Convert to millions for better readability
            $data[] = round($revenue / 1000000, 2);
        }

        return [
            'datasets' => [
                [
                    'label'           => 'Revenue (Million IRR)',
                    'data'            => $data,
                    'backgroundColor' => 'rgba(34, 197, 94, 0.1)',
                    'borderColor'     => 'rgb(34, 197, 94)',
                    'borderWidth'     => 2,
                    'fill'            => true,
                ],
            ],
            'labels'   => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}

