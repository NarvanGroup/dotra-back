<?php

namespace App\Filament\Resources\Collaterals\Schemas;

use App\Enums\CollateralType;
use App\Models\Application;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class CollateralForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Select::make('application_id')
                    ->relationship('application', 'id')
                    ->getOptionLabelFromRecordUsing(fn(Application $record
                    ) => "{$record->customer->first_name} {$record->customer->last_name} - ".number_format($record->principal_amount ?? $record->total_payable_amount ?? 0)." IRR")
                    ->searchable()
                    ->preload()
                    ->required()
                    ->live()
                    ->afterStateUpdated(function ($state, callable $set) {
                        if ($state) {
                            $application = Application::find($state);
                            if ($application) {
                                $set('vendor_id', $application->vendor_id);
                                $set('customer_id', $application->customer_id);
                            }
                        }
                    }),

                // Fields to store vendor and customer IDs automatically but allowing manual override if needed
                Select::make('vendor_id')
                    ->relationship('vendor', 'name')
                    ->required()
                    ->disabled() // Kept disabled for UI consistency, but ensure it's hydrated
                    ->dehydrated(),
                Select::make('customer_id')
                    ->relationship('customer', 'first_name')
                    ->getOptionLabelFromRecordUsing(fn($record) => "{$record->first_name} {$record->last_name}")
                    ->required()
                    ->disabled() // Kept disabled for UI consistency, but ensure it's hydrated
                    ->dehydrated(),

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
}
