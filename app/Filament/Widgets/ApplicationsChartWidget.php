<?php

namespace App\Filament\Widgets;

use App\Models\Application;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class ApplicationsChartWidget extends ChartWidget
{
    protected ?string $heading = 'Applications Overview';

    protected static ?int $sort = 2;

    protected function getData(): array
    {
        // Get data for the last 12 months
        $data = [];
        $labels = [];

        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $labels[] = $date->format('M Y');
            $data[] = Application::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
        }

        return [
            'datasets' => [
                [
                    'label'           => 'Applications',
                    'data'            => $data,
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'borderColor'     => 'rgb(59, 130, 246)',
                    'borderWidth'     => 2,
                    'fill'            => true,
                ],
            ],
            'labels'   => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}

