<?php

namespace App\Filament\Resources\CustomerResource\RelationManagers;

use Filament\Actions;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class VendorsRelationManager extends RelationManager
{
    protected static string $relationship = 'vendors';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                \Filament\Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->formatStateUsing(fn($state) => $state?->getLabel() ?? $state),
                Tables\Columns\TextColumn::make('industry')
                    ->formatStateUsing(fn($state) => $state?->getLabel() ?? $state)
                    ->limit(30),
                Tables\Columns\TextColumn::make('mobile')
                    ->icon('heroicon-o-phone'),
                Tables\Columns\TextColumn::make('email')
                    ->icon('heroicon-o-envelope'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Actions\AttachAction::make()
                    ->preloadRecordSelect(),
            ])
            ->actions([
                Actions\ViewAction::make(),
                Actions\DetachAction::make(),
            ])
            ->bulkActions([
                Actions\BulkActionGroup::make([
                    Actions\DetachBulkAction::make(),
                ]),
            ]);
    }
}
