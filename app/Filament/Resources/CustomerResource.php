<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CustomerResource\Pages;
use App\Filament\Resources\CustomerResource\RelationManagers;
use App\Models\Customer;
use BackedEnum;
use Filament\Actions;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use UnitEnum;

class CustomerResource extends Resource
{
    protected static ?string $model = Customer::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-user';

    protected static string|UnitEnum|null $navigationGroup = 'Customer Management';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                \Filament\Schemas\Components\Section::make('Personal Information')
                    ->schema([
                        \Filament\Forms\Components\TextInput::make('national_code')
                            ->label('National Code')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(10)
                            ->minLength(10)
                            ->numeric()
                            ->columnSpan(1),

                        \Filament\Forms\Components\TextInput::make('mobile')
                            ->label('Mobile Number')
                            ->required()
                            ->tel()
                            ->maxLength(11)
                            ->columnSpan(1),

                        \Filament\Forms\Components\TextInput::make('first_name')
                            ->required()
                            ->maxLength(255)
                            ->columnSpan(1),

                        \Filament\Forms\Components\TextInput::make('last_name')
                            ->required()
                            ->maxLength(255)
                            ->columnSpan(1),

                        \Filament\Forms\Components\DatePicker::make('birth_date')
                            ->required()
                            ->native(false)
                            ->maxDate(now()->subYears(18))
                            ->displayFormat('Y-m-d')
                            ->columnSpan(1),

                        \Filament\Forms\Components\TextInput::make('email')
                            ->email()
                            ->maxLength(255)
                            ->columnSpan(1),
                    ])
                    ->columns(2),

                \Filament\Schemas\Components\Section::make('Contact Information')
                    ->schema([
                        \Filament\Forms\Components\Textarea::make('address')
                            ->maxLength(65535)
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),

                \Filament\Schemas\Components\Section::make('Authentication')
                    ->schema([
                        \Filament\Forms\Components\TextInput::make('password')
                            ->password()
                            ->maxLength(255)
                            ->dehydrated(fn($state) => filled($state))
                            ->revealable()
                            ->columnSpan(1),
                    ])
                    ->columns(2)
                    ->collapsible()
                    ->collapsed(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('national_code')
                    ->label('National Code')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->tooltip('Click to copy'),

                Tables\Columns\TextColumn::make('first_name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('last_name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('mobile')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->icon('heroicon-o-phone'),

                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->toggleable()
                    ->icon('heroicon-o-envelope'),

                Tables\Columns\TextColumn::make('birth_date')
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('applications_count')
                    ->counts('applications')
                    ->label('Applications')
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('creditScores_count')
                    ->counts('creditScores')
                    ->label('Credit Scores')
                    ->badge()
                    ->color('success'),

                Tables\Columns\TextColumn::make('vendors_count')
                    ->counts('vendors')
                    ->label('Vendors')
                    ->badge()
                    ->color('warning'),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\Filter::make('has_applications')
                    ->label('Has Applications')
                    ->query(fn($query) => $query->has('applications')),

                Tables\Filters\Filter::make('has_credit_scores')
                    ->label('Has Credit Scores')
                    ->query(fn($query) => $query->has('creditScores')),

                Tables\Filters\Filter::make('created_at')
                    ->form([
                        \Filament\Forms\Components\DatePicker::make('created_from')
                            ->native(false),
                        \Filament\Forms\Components\DatePicker::make('created_until')
                            ->native(false),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['created_from'], fn($q, $date) => $q->whereDate('created_at', '>=', $date))
                            ->when($data['created_until'], fn($q, $date) => $q->whereDate('created_at', '<=', $date));
                    }),
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
                \Filament\Schemas\Components\Section::make('Personal Information')
                    ->schema([
                        \Filament\Infolists\Components\TextEntry::make('national_code')
                            ->label('National Code')
                            ->copyable(),
                        \Filament\Infolists\Components\TextEntry::make('first_name'),
                        \Filament\Infolists\Components\TextEntry::make('last_name'),
                        \Filament\Infolists\Components\TextEntry::make('mobile')
                            ->icon('heroicon-o-phone'),
                        \Filament\Infolists\Components\TextEntry::make('email')
                            ->icon('heroicon-o-envelope'),
                        \Filament\Infolists\Components\TextEntry::make('birth_date')
                            ->date(),
                    ])
                    ->columns(2),

                \Filament\Schemas\Components\Section::make('Contact Information')
                    ->schema([
                        \Filament\Infolists\Components\TextEntry::make('address')
                            ->columnSpanFull(),
                    ]),

                \Filament\Schemas\Components\Section::make('Statistics')
                    ->schema([
                        \Filament\Infolists\Components\TextEntry::make('applications_count')
                            ->label('Total Applications')
                            ->state(fn($record) => $record->applications()->count())
                            ->badge()
                            ->color('info'),
                        \Filament\Infolists\Components\TextEntry::make('creditScores_count')
                            ->label('Total Credit Scores')
                            ->state(fn($record) => $record->creditScores()->count())
                            ->badge()
                            ->color('success'),
                        \Filament\Infolists\Components\TextEntry::make('vendors_count')
                            ->label('Associated Vendors')
                            ->state(fn($record) => $record->vendors()->count())
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
            RelationManagers\CreditScoresRelationManager::class,
            RelationManagers\VendorsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListCustomers::route('/'),
            'create' => Pages\CreateCustomer::route('/create'),
            'view'   => Pages\ViewCustomer::route('/{record}'),
            'edit'   => Pages\EditCustomer::route('/{record}/edit'),
        ];
    }
}
