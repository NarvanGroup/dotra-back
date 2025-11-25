<?php

namespace App\Filament\Resources\Collaterals\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;

class CollateralsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('vendor.name')
                    ->label('Vendor')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('customer.first_name')
                    ->label('Customer')
                    ->formatStateUsing(fn($record) => $record->customer ? "{$record->customer->first_name} {$record->customer->last_name}" : '-')
                    ->sortable()
                    ->searchable(['customer.first_name', 'customer.last_name'])
                    ->toggleable(),
                TextColumn::make('type')
                    ->badge()
                    ->sortable(),
                TextColumn::make('file_path')
                    ->label('File')
                    ->formatStateUsing(fn() => 'View File')
                    ->url(fn($record) => Storage::url($record->file_path))
                    ->openUrlInNewTab()
                    ->icon('heroicon-o-document')
                    ->color('primary'),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
