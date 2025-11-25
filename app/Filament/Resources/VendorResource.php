<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VendorResource\Pages;
use App\Filament\Resources\VendorResource\RelationManagers;
use App\Models\Vendor;
use App\Models\Vendor\Industry;
use App\Models\Vendor\VendorType;
use BackedEnum;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use UnitEnum;

class VendorResource extends Resource
{
    protected static ?string $model = Vendor::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-building-storefront';

    protected static string|UnitEnum|null $navigationGroup = 'Vendor Management';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                \Filament\Schemas\Components\Section::make('Business Information')
                    ->schema([
                        \Filament\Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (Forms\Get $get, Forms\Set $set, ?string $state) {
                                if (!$get('slug')) {
                                    $set('slug', Str::slug($state));
                                }
                            })
                            ->columnSpan(1),

                        \Filament\Forms\Components\TextInput::make('slug')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255)
                            ->columnSpan(1),

                        \Filament\Forms\Components\Select::make('type')
                            ->label('Vendor Type')
                            ->options(VendorType::class)
                            ->required()
                            ->native(false)
                            ->columnSpan(1),

                        \Filament\Forms\Components\Select::make('industry')
                            ->options(Industry::class)
                            ->required()
                            ->native(false)
                            ->searchable()
                            ->columnSpan(1),

                        \Filament\Forms\Components\TextInput::make('national_code')
                            ->label('National/Company Code')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(11)
                            ->numeric()
                            ->columnSpan(1),

                        \Filament\Forms\Components\TextInput::make('business_license_code')
                            ->maxLength(255)
                            ->columnSpan(1),

                        \Filament\Forms\Components\TextInput::make('reffered_from')
                            ->label('Referred From')
                            ->maxLength(255)
                            ->columnSpan(1),
                    ])
                    ->columns(2),

                \Filament\Schemas\Components\Section::make('Owner Information')
                    ->schema([
                        \Filament\Forms\Components\TextInput::make('owner_first_name')
                            ->maxLength(255)
                            ->columnSpan(1),

                        \Filament\Forms\Components\TextInput::make('owner_last_name')
                            ->maxLength(255)
                            ->columnSpan(1),

                        \Filament\Forms\Components\DatePicker::make('owner_birth_date')
                            ->native(false)
                            ->maxDate(now()->subYears(18))
                            ->displayFormat('Y-m-d')
                            ->columnSpan(1),
                    ])
                    ->columns(2)
                    ->collapsible(),

                \Filament\Schemas\Components\Section::make('Contact Information')
                    ->schema([
                        \Filament\Forms\Components\TextInput::make('mobile')
                            ->label('Mobile Number')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->tel()
                            ->maxLength(11)
                            ->columnSpan(1),

                        \Filament\Forms\Components\TextInput::make('phone_number')
                            ->tel()
                            ->maxLength(255)
                            ->columnSpan(1),

                        \Filament\Forms\Components\TextInput::make('email')
                            ->email()
                            ->maxLength(255)
                            ->columnSpan(1),

                        \Filament\Forms\Components\TextInput::make('website_url')
                            ->url()
                            ->maxLength(255)
                            ->columnSpan(1),
                    ])
                    ->columns(2),

                \Filament\Schemas\Components\Section::make('Authentication')
                    ->schema([
                        \Filament\Forms\Components\TextInput::make('password')
                            ->password()
                            ->maxLength(255)
                            ->dehydrated(fn($state) => filled($state))
                            ->revealable(),
                    ])
                    ->collapsible()
                    ->collapsed(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('slug')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->formatStateUsing(fn($state) => $state?->getLabel() ?? $state)
                    ->color(fn($state) => match ($state) {
                        VendorType::INDIVIDUAL => 'info',
                        VendorType::LEGAL => 'success',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('industry')
                    ->searchable()
                    ->formatStateUsing(fn($state) => $state?->getLabel() ?? $state)
                    ->wrap()
                    ->limit(30),

                Tables\Columns\TextColumn::make('national_code')
                    ->label('National Code')
                    ->searchable()
                    ->copyable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('mobile')
                    ->searchable()
                    ->icon('heroicon-o-phone')
                    ->copyable(),

                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->icon('heroicon-o-envelope')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('applications_count')
                    ->counts('applications')
                    ->label('Applications')
                    ->badge()
                    ->color('info')
                    ->sortable(),

                Tables\Columns\TextColumn::make('customers_count')
                    ->counts('customers')
                    ->label('Customers')
                    ->badge()
                    ->color('success')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->options(VendorType::class)
                    ->native(false),

                Tables\Filters\SelectFilter::make('industry')
                    ->options(Industry::class)
                    ->native(false)
                    ->searchable(),

                Tables\Filters\Filter::make('has_applications')
                    ->label('Has Applications')
                    ->query(fn($query) => $query->has('applications')),
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

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->schema([
                \Filament\Schemas\Components\Section::make('Business Information')
                    ->schema([
                        \Filament\Infolists\Components\TextEntry::make('name')
                            ->weight('bold')
                            ->size('lg'),
                        \Filament\Infolists\Components\TextEntry::make('slug')
                            ->copyable(),
                        \Filament\Infolists\Components\TextEntry::make('type')
                            ->badge()
                            ->formatStateUsing(fn($state) => $state?->getLabel() ?? $state),
                        \Filament\Infolists\Components\TextEntry::make('industry')
                            ->formatStateUsing(fn($state) => $state?->getLabel() ?? $state),
                        \Filament\Infolists\Components\TextEntry::make('national_code')
                            ->copyable(),
                        \Filament\Infolists\Components\TextEntry::make('business_license_code'),
                        \Filament\Infolists\Components\TextEntry::make('reffered_from')
                            ->label('Referred From'),
                    ])
                    ->columns(2),

                \Filament\Schemas\Components\Section::make('Owner Information')
                    ->schema([
                        \Filament\Infolists\Components\TextEntry::make('owner_first_name'),
                        \Filament\Infolists\Components\TextEntry::make('owner_last_name'),
                        \Filament\Infolists\Components\TextEntry::make('owner_birth_date')
                            ->date(),
                    ])
                    ->columns(3)
                    ->collapsible(),

                \Filament\Schemas\Components\Section::make('Contact Information')
                    ->schema([
                        \Filament\Infolists\Components\TextEntry::make('mobile')
                            ->icon('heroicon-o-phone')
                            ->copyable(),
                        \Filament\Infolists\Components\TextEntry::make('phone_number')
                            ->icon('heroicon-o-phone'),
                        \Filament\Infolists\Components\TextEntry::make('email')
                            ->icon('heroicon-o-envelope')
                            ->copyable(),
                        \Filament\Infolists\Components\TextEntry::make('website_url')
                            ->icon('heroicon-o-globe-alt')
                            ->url(fn($state) => $state)
                            ->openUrlInNewTab(),
                    ])
                    ->columns(2),

                \Filament\Schemas\Components\Section::make('Statistics')
                    ->schema([
                        \Filament\Infolists\Components\TextEntry::make('applications_count')
                            ->label('Total Applications')
                            ->state(fn($record) => $record->applications()->count())
                            ->badge()
                            ->color('info'),
                        \Filament\Infolists\Components\TextEntry::make('customers_count')
                            ->label('Total Customers')
                            ->state(fn($record) => $record->customers()->count())
                            ->badge()
                            ->color('success'),
                        \Filament\Infolists\Components\TextEntry::make('commission_rate')
                            ->label('Commission Rate')
                            ->state(fn($record) => $record->industry?->commissionRate().'%')
                            ->badge()
                            ->color('warning'),
                    ])
                    ->columns(3),

                \Filament\Schemas\Components\Section::make('Timestamps')
                    ->schema([
                        \Filament\Infolists\Components\TextEntry::make('created_at')
                            ->dateTime(),
                        \Filament\Infolists\Components\TextEntry::make('updated_at')
                            ->dateTime(),
                    ])
                    ->columns(2)
                    ->collapsed(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\ApplicationsRelationManager::class,
            RelationManagers\CustomersRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListVendors::route('/'),
            'create' => Pages\CreateVendor::route('/create'),
            'view'   => Pages\ViewVendor::route('/{record}'),
            'edit'   => Pages\EditVendor::route('/{record}/edit'),
        ];
    }
}
