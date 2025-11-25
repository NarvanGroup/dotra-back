<?php

namespace App\Filament\Widgets;

use App\Models\Application;
use App\Models\Application\Status;
use Filament\Widgets\ChartWidget;

class ApplicationStatusWidget extends ChartWidget
{
    protected ?string $heading = 'Applications by Status';

    protected static ?int $sort = 6;

    protected function getData(): array
    {
        $statuses = [
            'Terms Suggested'  => Application::where('status', Status::TERMS_SUGGESTED)->count(),
            'Vendor Adjusting' => Application::where('status', Status::VENDOR_ADJUSTING)->count(),
            'Approved'         => Application::where('status', Status::APPROVED)->count(),
            'In Repayment'     => Application::where('status', Status::IN_REPAYMENT)->count(),
            'Overdue'          => Application::where('status', Status::OVERDUE)->count(),
            'Repaid'           => Application::where('status', Status::REPAID)->count(),
        ];

        return [
            'datasets' => [
                [
                    'label'           => 'Applications',
                    'data'            => array_values($statuses),
                    'backgroundColor' => [
                        'rgb(59, 130, 246)',   // blue - Terms Suggested
                        'rgb(251, 191, 36)',   // yellow - Vendor Adjusting
                        'rgb(34, 197, 94)',    // green - Approved
                        'rgb(99, 102, 241)',   // indigo - In Repayment
                        'rgb(239, 68, 68)',    // red - Overdue
                        'rgb(168, 85, 247)',   // purple - Repaid
                    ],
                ],
            ],
            'labels'   => array_keys($statuses),
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }
}

