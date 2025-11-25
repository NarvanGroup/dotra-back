<?php

namespace App\Filament\Resources;

use App\Enums\CreditScore\CreditScoreStatus;
use App\Filament\Resources\CreditScoreResource\Pages;
use App\Models\CreditScore;
use App\Models\Customer;
use BackedEnum;
use Filament\Actions;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class CreditScoreResource extends Resource
{
    protected static ?string $model = CreditScore::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-chart-bar';

    protected static string|UnitEnum|null $navigationGroup = 'Credit Management';

    protected static ?int $navigationSort = 5;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['customer', 'initiator']);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                \Filament\Schemas\Components\Section::make('Credit Score Information')
                    ->schema([
                        \Filament\Forms\Components\Select::make('customer_id')
                            ->label('Customer')
                            ->relationship('customer', 'first_name')
                            ->getOptionLabelFromRecordUsing(fn(Customer $record) => "{$record->first_name} {$record->last_name} ({$record->national_code})")
                            ->searchable(['first_name', 'last_name', 'national_code'])
                            ->required()
                            ->native(false)
                            ->columnSpan(1)
                            ->preload(),

                        \Filament\Forms\Components\DatePicker::make('issued_on')
                            ->label('Issued Date')
                            ->required()
                            ->native(false)
                            ->default(now())
                            ->displayFormat('Y-m-d')
                            ->columnSpan(1),

                        \Filament\Forms\Components\Select::make('status')
                            ->options(CreditScoreStatus::class)
                            ->required()
                            ->native(false)
                            ->default(CreditScoreStatus::PENDING)
                            ->columnSpan(1),

                        \Filament\Forms\Components\TextInput::make('overall_score')
                            ->label('Overall Score')
                            ->numeric()
                            ->minValue(300)
                            ->maxValue(850)
                            ->required()
                            ->columnSpan(1),
                    ])
                    ->columns(2),

                \Filament\Schemas\Components\Section::make('Initiator Information')
                    ->schema([
                        \Filament\Forms\Components\MorphToSelect::make('initiator')
                            ->label('Initiated By')
                            ->types([
                                \Filament\Forms\Components\MorphToSelect\Type::make(\App\Models\Vendor::class)
                                    ->titleAttribute('name')
                                    ->getOptionLabelFromRecordUsing(fn($record) => $record->name),
                                \Filament\Forms\Components\MorphToSelect\Type::make(\App\Models\Customer::class)
                                    ->titleAttribute('first_name')
                                    ->getOptionLabelFromRecordUsing(fn($record) => "{$record->first_name} {$record->last_name}"),
                            ])
                            ->searchable()
                            ->preload()
                            ->required()
                            ->native(false),
                    ])
                    ->description('The entity (Vendor or Customer) that initiated this credit score request'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->limit(8)
                    ->toggleable(),

                Tables\Columns\TextColumn::make('customer.first_name')
                    ->label('Customer')
                    ->formatStateUsing(fn($record) => "{$record->customer->first_name} {$record->customer->last_name}")
                    ->searchable(['first_name', 'last_name'])
                    ->sortable(),

                Tables\Columns\TextColumn::make('overall_score')
                    ->label('Score')
                    ->sortable()
                    ->badge()
                    ->color(fn($state) => match (true) {
                        $state >= 750 => 'success',
                        $state >= 650 => 'warning',
                        $state >= 550 => 'info',
                        default => 'danger',
                    })
                    ->formatStateUsing(fn($state) => $state ?: 'N/A')
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn($state) => $state?->getLabel() ?? $state)
                    ->color(fn($state) => match ($state) {
                        CreditScoreStatus::PENDING => 'gray',
                        CreditScoreStatus::PROCESSING => 'info',
                        CreditScoreStatus::COMPLETED => 'success',
                        CreditScoreStatus::FAILED => 'danger',
                        default => 'gray',
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('initiator_type')
                    ->label('Initiated By')
                    ->formatStateUsing(fn($state) => class_basename($state))
                    ->badge()
                    ->color('info')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('issued_on')
                    ->label('Issued Date')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(CreditScoreStatus::class)
                    ->native(false),

                Tables\Filters\Filter::make('overall_score')
                    ->form([
                        \Filament\Forms\Components\TextInput::make('score_from')
                            ->numeric()
                            ->minValue(300)
                            ->maxValue(850),
                        \Filament\Forms\Components\TextInput::make('score_to')
                            ->numeric()
                            ->minValue(300)
                            ->maxValue(850),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['score_from'], fn($q, $score) => $q->where('overall_score', '>=', $score))
                            ->when($data['score_to'], fn($q, $score) => $q->where('overall_score', '<=', $score));
                    }),

                Tables\Filters\SelectFilter::make('initiator_type')
                    ->label('Initiated By Type')
                    ->options([
                        'App\\Models\\Vendor' => 'Vendor',
                        'App\\Models\\Customer' => 'Customer',
                    ])
                    ->native(false),

                Tables\Filters\Filter::make('issued_on')
                    ->form([
                        \Filament\Forms\Components\DatePicker::make('issued_from')
                            ->native(false),
                        \Filament\Forms\Components\DatePicker::make('issued_until')
                            ->native(false),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['issued_from'], fn($q, $date) => $q->whereDate('issued_on', '>=', $date))
                            ->when($data['issued_until'], fn($q, $date) => $q->whereDate('issued_on', '<=', $date));
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
            ->defaultSort('issued_on', 'desc');
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->schema([
                \Filament\Schemas\Components\Section::make('Credit Score Overview')
                    ->schema([
                        \Filament\Infolists\Components\TextEntry::make('id')
                            ->label('Credit Score ID')
                            ->copyable(),
                        \Filament\Infolists\Components\TextEntry::make('overall_score')
                            ->label('Overall Score')
                            ->badge()
                            ->size('lg')
                            ->weight('bold')
                            ->color(fn($state) => match (true) {
                                $state >= 750 => 'success',
                                $state >= 650 => 'warning',
                                $state >= 550 => 'info',
                                default => 'danger',
                            }),
                        \Filament\Infolists\Components\TextEntry::make('status')
                            ->badge()
                            ->formatStateUsing(fn($state) => $state?->getLabel() ?? $state),
                    ])
                    ->columns(3),

                \Filament\Schemas\Components\Section::make('Customer Information')
                    ->schema([
                        \Filament\Infolists\Components\TextEntry::make('customer.first_name')
                            ->label('Customer Name')
                            ->formatStateUsing(fn($record) => "{$record->customer->first_name} {$record->customer->last_name}")
                            ->url(fn($record) => CustomerResource::getUrl('view', ['record' => $record->customer]))
                            ->color('primary'),
                        \Filament\Infolists\Components\TextEntry::make('customer.national_code')
                            ->label('National Code')
                            ->copyable(),
                        \Filament\Infolists\Components\TextEntry::make('customer.mobile')
                            ->icon('heroicon-o-phone')
                            ->copyable(),
                    ])
                    ->columns(3),

                \Filament\Schemas\Components\Section::make('Initiator Information')
                    ->schema([
                        \Filament\Infolists\Components\TextEntry::make('initiator_type')
                            ->label('Initiated By Type')
                            ->formatStateUsing(fn($state) => class_basename($state))
                            ->badge(),
                        \Filament\Infolists\Components\TextEntry::make('initiator.name')
                            ->label('Initiator Name')
                            ->formatStateUsing(fn($record) => $record->initiator instanceof \App\Models\Vendor
                                ? $record->initiator->name
                                : "{$record->initiator->first_name} {$record->initiator->last_name}"
                            )
                            ->color('primary'),
                    ])
                    ->columns(2),

                \Filament\Schemas\Components\Section::make('Score Rating')
                    ->schema([
                        \Filament\Infolists\Components\TextEntry::make('rating')
                            ->label('Credit Rating')
                            ->state(fn($record) => match (true) {
                                $record->overall_score >= 800 => 'Excellent',
                                $record->overall_score >= 750 => 'Very Good',
                                $record->overall_score >= 700 => 'Good',
                                $record->overall_score >= 650 => 'Fair',
                                $record->overall_score >= 600 => 'Poor',
                                default => 'Very Poor',
                            })
                            ->badge()
                            ->color(fn($record) => match (true) {
                                $record->overall_score >= 750 => 'success',
                                $record->overall_score >= 650 => 'warning',
                                $record->overall_score >= 550 => 'info',
                                default => 'danger',
                            })
                            ->size('lg'),
                    ])
                    ->description('Credit score interpretation based on standard ranges'),

                \Filament\Schemas\Components\Section::make('Dates')
                    ->schema([
                        \Filament\Infolists\Components\TextEntry::make('issued_on')
                            ->date(),
                        \Filament\Infolists\Components\TextEntry::make('created_at')
                            ->dateTime(),
                        \Filament\Infolists\Components\TextEntry::make('updated_at')
                            ->dateTime(),
                    ])
                    ->columns(3)
                    ->collapsed(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCreditScores::route('/'),
            'create' => Pages\CreateCreditScore::route('/create'),
            'view'  => Pages\ViewCreditScore::route('/{record}'),
            'edit'  => Pages\EditCreditScore::route('/{record}/edit'),
        ];
    }
}
