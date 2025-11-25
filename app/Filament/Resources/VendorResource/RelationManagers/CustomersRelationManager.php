<?php

namespace App\Filament\Resources\VendorResource\RelationManagers;

use Filament\Actions;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class CustomersRelationManager extends RelationManager
{
    protected static string $relationship = 'customers';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                \Filament\Forms\Components\TextInput::make('first_name')
                    ->required()
                    ->maxLength(255),
                \Filament\Forms\Components\TextInput::make('last_name')
                    ->required()
                    ->maxLength(255),
                \Filament\Forms\Components\TextInput::make('national_code')
                    ->required()
                    ->maxLength(10),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('first_name')
            ->columns([
                Tables\Columns\TextColumn::make('national_code')
                    ->searchable()
                    ->copyable(),
                Tables\Columns\TextColumn::make('first_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('last_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('mobile')
                    ->icon('heroicon-o-phone')
                    ->copyable(),
                Tables\Columns\TextColumn::make('email')
                    ->icon('heroicon-o-envelope'),
                Tables\Columns\TextColumn::make('applications_count')
                    ->counts('applications')
                    ->badge(),
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
