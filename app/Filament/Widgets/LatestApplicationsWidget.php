<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\ApplicationResource;
use App\Models\Application;
use Filament\Actions;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestApplicationsWidget extends BaseWidget
{
    protected static ?int $sort = 4;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Application::query()
                    ->latest()
                    ->limit(5)
            )
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('Application ID')
                    ->limit(8)
                    ->copyable(),

                Tables\Columns\TextColumn::make('customer.first_name')
                    ->label('Customer')
                    ->formatStateUsing(fn($record) => "{$record->customer->first_name} {$record->customer->last_name}"
                    ),

                Tables\Columns\TextColumn::make('vendor.name')
                    ->label('Vendor'),

                Tables\Columns\TextColumn::make('principal_amount')
                    ->label('Principal')
                    ->money('IRR'),
                Tables\Columns\TextColumn::make('total_payable_amount')
                    ->label('Total Payable')
                    ->money('IRR'),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn($state) => $state?->getLabel() ?? $state),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->since(),
            ])
            ->actions([
                Actions\Action::make('view')
                    ->url(fn(Application $record): string => ApplicationResource::getUrl('view', ['record' => $record]))
                    ->icon('heroicon-o-eye'),
            ]);
    }

    protected function getTableHeading(): ?string
    {
        return 'Latest Applications';
    }
}

