<?php

namespace App\Filament\Resources\ApplicationResource\RelationManagers;

use Filament\Actions;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class InstallmentsRelationManager extends RelationManager
{
    protected static string $relationship = 'installments';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                \Filament\Forms\Components\TextInput::make('installment_number')
                    ->required()
                    ->numeric()
                    ->minValue(1),

                \Filament\Forms\Components\TextInput::make('amount')
                    ->required()
                    ->numeric()
                    ->prefix('IRR')
                    ->minValue(0),

                \Filament\Forms\Components\DateTimePicker::make('due_date')
                    ->required()
                    ->native(false),

                \Filament\Forms\Components\DateTimePicker::make('paid_at')
                    ->native(false),

                \Filament\Forms\Components\Select::make('status')
                    ->options([
                        'pending'   => 'Pending',
                        'paid'      => 'Paid',
                        'overdue'   => 'Overdue',
                        'cancelled' => 'Cancelled',
                    ])
                    ->required()
                    ->native(false)
                    ->default('pending'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('installment_number')
            ->columns([
                Tables\Columns\TextColumn::make('installment_number')
                    ->label('#')
                    ->sortable()
                    ->alignCenter()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('amount')
                    ->money('IRR')
                    ->sortable(),

                Tables\Columns\TextColumn::make('due_date')
                    ->dateTime('Y-m-d')
                    ->sortable()
                    ->color(fn($record) => $record->due_date < now() && $record->status !== 'paid' ? 'danger' : 'gray'
                    ),

                Tables\Columns\TextColumn::make('paid_at')
                    ->dateTime('Y-m-d H:i')
                    ->placeholder('Not paid'),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn($state) => match ($state) {
                        'pending' => 'warning',
                        'paid' => 'success',
                        'overdue' => 'danger',
                        'cancelled' => 'gray',
                        default => 'info',
                    })
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Actions\CreateAction::make(),
            ])
            ->actions([
                Actions\Action::make('mark_as_paid')
                    ->label('Mark as Paid')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn($record) => $record->status !== 'paid')
                    ->action(function ($record) {
                        $record->update([
                            'status'  => 'paid',
                            'paid_at' => now(),
                        ]);
                    }),
                Actions\ViewAction::make(),
                Actions\EditAction::make(),
                Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('installment_number', 'asc');
    }
}
