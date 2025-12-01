<?php

namespace App\Filament\Resources\CustomerResource\RelationManagers;

use App\Models\Application\Status;
use Filament\Actions;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class ApplicationsRelationManager extends RelationManager
{
    protected static string $relationship = 'applications';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                \Filament\Forms\Components\Select::make('vendor_id')
                    ->relationship('vendor', 'name')
                    ->required()
                    ->native(false)
                    ->preload(),

                \Filament\Forms\Components\Select::make('credit_score_id')
                    ->relationship('creditScore', 'id')
                    ->getOptionLabelFromRecordUsing(fn($record) => "Score: {$record->overall_score}")
                    ->required()
                    ->native(false)
                    ->preload(),

                \Filament\Forms\Components\TextInput::make('principal_amount')
                    ->label('Principal Amount')
                    ->numeric()
                    ->prefix('IRR'),

                \Filament\Forms\Components\TextInput::make('down_payment_amount')
                    ->label('Down Payment Amount')
                    ->numeric()
                    ->prefix('IRR'),

                \Filament\Forms\Components\TextInput::make('number_of_installments')
                    ->numeric()
                    ->minValue(1)
                    ->maxValue(60),

                \Filament\Forms\Components\TextInput::make('interest_rate')
                    ->numeric()
                    ->suffix('%')
                    ->step(0.01),

                \Filament\Forms\Components\Select::make('status')
                    ->options(Status::class)
                    ->required()
                    ->native(false),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('Application ID')
                    ->limit(8)
                    ->copyable(),
                Tables\Columns\TextColumn::make('vendor.name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('principal_amount')
                    ->label('Principal')
                    ->money('IRR'),
                Tables\Columns\TextColumn::make('total_payable_amount')
                    ->label('Total Payable')
                    ->money('IRR'),
                Tables\Columns\TextColumn::make('number_of_installments')
                    ->label('Installments')
                    ->suffix(' months'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn($state) => $state?->getLabel() ?? $state),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Actions\CreateAction::make(),
            ])
            ->actions([
                Actions\ViewAction::make(),
                Actions\EditAction::make(),
                Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
