<?php

namespace App\Filament\Resources\ApplicationResource\RelationManagers;

use App\Enums\CollateralType;
use App\Models\Collateral;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;

class CollateralsRelationManager extends RelationManager
{
    protected static string $relationship = 'collaterals';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Select::make('type')
                    ->options(CollateralType::class)
                    ->required(),
                FileUpload::make('file_path')
                    ->label('Document/Photo')
                    ->acceptedFileTypes(['image/*', 'application/pdf'])
                    ->required()
                    ->directory('collaterals')
                    ->disk('public')
                    ->visibility('public')
                    ->openable()
                    ->downloadable(),
                Textarea::make('description')
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('type')
            ->recordTitle(fn(Collateral $record) => $record->type->getLabel())
            ->columns([
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
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make(),
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
