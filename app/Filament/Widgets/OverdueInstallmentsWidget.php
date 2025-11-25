<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\InstallmentResource;
use App\Models\Installment;
use Filament\Actions;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class OverdueInstallmentsWidget extends BaseWidget
{
    protected static ?int $sort = 5;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Installment::query()
                    ->where('due_date', '<', now())
                    ->where('status', '!=', 'paid')
                    ->orderBy('due_date', 'asc')
                    ->limit(10)
            )
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->limit(8)
                    ->copyable(),

                Tables\Columns\TextColumn::make('application.customer.first_name')
                    ->label('Customer')
                    ->formatStateUsing(fn($record) => "{$record->application->customer->first_name} {$record->application->customer->last_name}"
                    ),

                Tables\Columns\TextColumn::make('application.vendor.name')
                    ->label('Vendor'),

                Tables\Columns\TextColumn::make('installment_number')
                    ->label('#')
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('amount')
                    ->money('IRR'),

                Tables\Columns\TextColumn::make('due_date')
                    ->label('Due Date')
                    ->dateTime('Y-m-d')
                    ->color('danger'),

                Tables\Columns\TextColumn::make('days_overdue')
                    ->label('Days Overdue')
                    ->state(fn($record) => abs(now()->diffInDays($record->due_date, false)))
                    ->badge()
                    ->color('danger'),
            ])
            ->actions([
                Actions\Action::make('view')
                    ->url(fn(Installment $record): string => InstallmentResource::getUrl('view', ['record' => $record]))
                    ->icon('heroicon-o-eye'),

                Actions\Action::make('mark_as_paid')
                    ->label('Mark Paid')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(function (Installment $record) {
                        $record->update([
                            'status'  => 'paid',
                            'paid_at' => now(),
                        ]);
                    }),
            ]);
    }

    protected function getTableHeading(): ?string
    {
        return 'Overdue Installments';
    }
}

