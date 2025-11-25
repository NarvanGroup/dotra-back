<?php

namespace App\Filament\Resources\CustomerResource\RelationManagers;

use App\Enums\CreditScore\CreditScoreStatus;
use Filament\Actions;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class CreditScoresRelationManager extends RelationManager
{
    protected static string $relationship = 'creditScores';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                \Filament\Forms\Components\DatePicker::make('issued_on')
                    ->required()
                    ->native(false)
                    ->default(now()),

                \Filament\Forms\Components\Select::make('status')
                    ->options(CreditScoreStatus::class)
                    ->required()
                    ->native(false),

                \Filament\Forms\Components\TextInput::make('overall_score')
                    ->numeric()
                    ->minValue(300)
                    ->maxValue(850)
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('Score ID')
                    ->limit(8)
                    ->copyable(),
                Tables\Columns\TextColumn::make('overall_score')
                    ->label('Score')
                    ->badge()
                    ->color(fn($state) => match (true) {
                        $state >= 750 => 'success',
                        $state >= 650 => 'warning',
                        default => 'danger',
                    }),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn($state) => $state?->getLabel() ?? $state),
                Tables\Columns\TextColumn::make('initiator_type')
                    ->label('Initiated By')
                    ->formatStateUsing(fn($state) => class_basename($state))
                    ->badge(),
                Tables\Columns\TextColumn::make('issued_on')
                    ->date(),
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
            ->defaultSort('issued_on', 'desc');
    }
}
