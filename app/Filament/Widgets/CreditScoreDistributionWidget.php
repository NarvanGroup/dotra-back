<?php

namespace App\Filament\Widgets;

use App\Models\CreditScore;
use Filament\Widgets\ChartWidget;

class CreditScoreDistributionWidget extends ChartWidget
{
    protected ?string $heading = 'Credit Score Distribution';

    protected static ?int $sort = 3;

    protected function getData(): array
    {
        $ranges = [
            'Very Poor (300-549)' => CreditScore::whereBetween('overall_score', [300, 549])->count(),
            'Poor (550-599)'      => CreditScore::whereBetween('overall_score', [550, 599])->count(),
            'Fair (600-649)'      => CreditScore::whereBetween('overall_score', [600, 649])->count(),
            'Good (650-699)'      => CreditScore::whereBetween('overall_score', [650, 699])->count(),
            'Very Good (700-749)' => CreditScore::whereBetween('overall_score', [700, 749])->count(),
            'Excellent (750-850)' => CreditScore::whereBetween('overall_score', [750, 850])->count(),
        ];

        return [
            'datasets' => [
                [
                    'label'           => 'Credit Scores',
                    'data'            => array_values($ranges),
                    'backgroundColor' => [
                        'rgb(239, 68, 68)',   // red - Very Poor
                        'rgb(249, 115, 22)',  // orange - Poor
                        'rgb(251, 191, 36)',  // yellow - Fair
                        'rgb(34, 197, 94)',   // green - Good
                        'rgb(59, 130, 246)',  // blue - Very Good
                        'rgb(168, 85, 247)',  // purple - Excellent
                    ],
                ],
            ],
            'labels'   => array_keys($ranges),
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}

