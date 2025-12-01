<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ApplicationResource\Pages;
use App\Filament\Resources\ApplicationResource\RelationManagers;
use App\Models\Application;
use App\Models\Application\Status;
use App\Models\Customer;
use BackedEnum;
use Filament\Actions;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use UnitEnum;

class ApplicationResource extends Resource
{
    protected static ?string $model = Application::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-document-text';

    protected static string|UnitEnum|null $navigationGroup = 'Application Management';

    protected static ?int $navigationSort = 4;

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()
            ->with(['customer', 'vendor', 'creditScore']);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                \Filament\Schemas\Components\Section::make('Application Details')
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

                        \Filament\Forms\Components\Select::make('vendor_id')
                            ->label('Vendor')
                            ->relationship('vendor', 'name')
                            ->searchable(['name', 'slug'])
                            ->required()
                            ->native(false)
                            ->columnSpan(1)
                            ->preload(),

                        \Filament\Forms\Components\Select::make('credit_score_id')
                            ->label('Credit Score')
                            ->relationship('creditScore', 'id')
                            ->getOptionLabelFromRecordUsing(fn($record) => "Score: {$record->overall_score} - {$record->issued_on->format('Y-m-d')}")
                            ->searchable()
                            ->required()
                            ->native(false)
                            ->columnSpan(1)
                            ->preload(),

                        \Filament\Forms\Components\Select::make('status')
                            ->options(Status::class)
                            ->required()
                            ->native(false)
                            ->columnSpan(1),
                    ])
                    ->columns(2),

                \Filament\Schemas\Components\Section::make('Requested Terms')
                    ->schema([
                        \Filament\Forms\Components\TextInput::make('principal_amount')
                            ->label('Principal Amount')
                            ->numeric()
                            ->prefix('IRR')
                            ->maxLength(255)
                            ->columnSpan(1),

                        \Filament\Forms\Components\TextInput::make('down_payment_amount')
                            ->label('Down Payment Amount')
                            ->numeric()
                            ->prefix('IRR')
                            ->maxLength(255)
                            ->columnSpan(1),

                        \Filament\Forms\Components\TextInput::make('number_of_installments')
                            ->label('Number of Installments')
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(60)
                            ->suffix('months')
                            ->columnSpan(1),

                        \Filament\Forms\Components\TextInput::make('interest_rate')
                            ->label('Interest Rate')
                            ->numeric()
                            ->suffix('%')
                            ->maxValue(100)
                            ->step(0.01)
                            ->columnSpan(1),
                    ])
                    ->columns(4),

                \Filament\Schemas\Components\Section::make('Suggested Terms')
                    ->schema([
                        \Filament\Forms\Components\TextInput::make('suggested_total_amount')
                            ->label('Suggested Total Amount')
                            ->numeric()
                            ->prefix('IRR')
                            ->disabled()
                            ->dehydrated()
                            ->columnSpan(1),

                        \Filament\Forms\Components\TextInput::make('suggested_number_of_installments')
                            ->label('Suggested Installments')
                            ->numeric()
                            ->suffix('months')
                            ->disabled()
                            ->dehydrated()
                            ->columnSpan(1),

                        \Filament\Forms\Components\TextInput::make('suggested_interest_rate')
                            ->label('Suggested Interest Rate')
                            ->numeric()
                            ->suffix('%')
                            ->disabled()
                            ->dehydrated()
                            ->step(0.01)
                            ->columnSpan(1),
                    ])
                    ->columns(3)
                    ->description('These values are automatically calculated and cannot be modified'),
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

                Tables\Columns\TextColumn::make('vendor.name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('principal_amount')
                    ->label('Principal')
                    ->money('IRR')
                    ->sortable(),

                Tables\Columns\TextColumn::make('total_payable_amount')
                    ->label('Total Payable')
                    ->money('IRR')
                    ->sortable(),

                Tables\Columns\TextColumn::make('number_of_installments')
                    ->label('Installments')
                    ->suffix(' months')
                    ->sortable()
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('interest_rate')
                    ->label('Rate')
                    ->suffix('%')
                    ->sortable()
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn($state) => $state?->getLabel() ?? $state)
                    ->color(fn($state) => match ($state) {
                        Status::TERMS_SUGGESTED => 'info',
                        Status::VENDOR_ADJUSTING => 'warning',
                        Status::APPROVED => 'success',
                        Status::IN_REPAYMENT => 'primary',
                        Status::OVERDUE => 'danger',
                        Status::REPAID => 'success',
                        default => 'gray',
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('creditScore.overall_score')
                    ->label('Credit Score')
                    ->badge()
                    ->color(fn($state) => match (true) {
                        $state >= 750 => 'success',
                        $state >= 650 => 'warning',
                        default => 'danger',
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('installments_count')
                    ->counts('installments')
                    ->label('Installments')
                    ->badge()
                    ->color('gray'),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(Status::class)
                    ->native(false),

                Tables\Filters\SelectFilter::make('vendor')
                    ->relationship('vendor', 'name')
                    ->searchable()
                    ->preload()
                    ->native(false),

                Tables\Filters\Filter::make('principal_amount')
                    ->form([
                        \Filament\Forms\Components\TextInput::make('amount_from')
                            ->numeric()
                            ->prefix('IRR'),
                        \Filament\Forms\Components\TextInput::make('amount_to')
                            ->numeric()
                            ->prefix('IRR'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['amount_from'], fn($q, $amount) => $q->where('principal_amount', '>=', $amount))
                            ->when($data['amount_to'], fn($q, $amount) => $q->where('principal_amount', '<=', $amount));
                    }),

                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Actions\ViewAction::make(),
                Actions\EditAction::make(),
                Actions\DeleteAction::make(),
                Actions\RestoreAction::make(),
                Actions\ForceDeleteAction::make(),
            ])
            ->bulkActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make(),
                    Actions\RestoreBulkAction::make(),
                    Actions\ForceDeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->schema([
                \Filament\Schemas\Components\Section::make('Application Information')
                    ->schema([
                        \Filament\Infolists\Components\TextEntry::make('id')
                            ->label('Application ID')
                            ->copyable(),
                        \Filament\Infolists\Components\TextEntry::make('status')
                            ->badge()
                            ->formatStateUsing(fn($state) => $state?->getLabel() ?? $state),
                    ])
                    ->columns(2),

                \Filament\Schemas\Components\Section::make('Parties')
                    ->schema([
                        \Filament\Infolists\Components\TextEntry::make('customer.first_name')
                            ->label('Customer')
                            ->formatStateUsing(fn($record) => "{$record->customer->first_name} {$record->customer->last_name}")
                            ->url(fn($record) => CustomerResource::getUrl('view', ['record' => $record->customer]))
                            ->color('primary'),
                        \Filament\Infolists\Components\TextEntry::make('vendor.name')
                            ->url(fn($record) => VendorResource::getUrl('view', ['record' => $record->vendor]))
                            ->color('primary'),
                        \Filament\Infolists\Components\TextEntry::make('creditScore.overall_score')
                            ->label('Credit Score')
                            ->badge()
                            ->url(fn($record) => CreditScoreResource::getUrl('view', ['record' => $record->creditScore]))
                            ->color('primary'),
                    ])
                    ->columns(3),

                \Filament\Schemas\Components\Section::make('Requested Terms')
                    ->schema([
                        \Filament\Infolists\Components\TextEntry::make('principal_amount')
                            ->label('Principal Amount')
                            ->money('IRR'),
                        \Filament\Infolists\Components\TextEntry::make('down_payment_amount')
                            ->label('Down Payment Amount')
                            ->money('IRR'),
                        \Filament\Infolists\Components\TextEntry::make('total_payable_amount')
                            ->label('Total Payable Amount')
                            ->money('IRR'),
                        \Filament\Infolists\Components\TextEntry::make('number_of_installments')
                            ->suffix(' months'),
                        \Filament\Infolists\Components\TextEntry::make('interest_rate')
                            ->suffix('%'),
                    ])
                    ->columns(5),

                \Filament\Schemas\Components\Section::make('Suggested Terms')
                    ->schema([
                        \Filament\Infolists\Components\TextEntry::make('suggested_total_amount')
                            ->money('IRR'),
                        \Filament\Infolists\Components\TextEntry::make('suggested_number_of_installments')
                            ->suffix(' months'),
                        \Filament\Infolists\Components\TextEntry::make('suggested_interest_rate')
                            ->suffix('%'),
                    ])
                    ->columns(3),

                \Filament\Schemas\Components\Section::make('Timestamps')
                    ->schema([
                        \Filament\Infolists\Components\TextEntry::make('created_at')
                            ->dateTime(),
                        \Filament\Infolists\Components\TextEntry::make('updated_at')
                            ->dateTime(),
                        \Filament\Infolists\Components\TextEntry::make('deleted_at')
                            ->dateTime(),
                    ])
                    ->columns(3)
                    ->collapsed(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\InstallmentsRelationManager::class,
            RelationManagers\CollateralsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListApplications::route('/'),
            'create' => Pages\CreateApplication::route('/create'),
            'view'   => Pages\ViewApplication::route('/{record}'),
            'edit'   => Pages\EditApplication::route('/{record}/edit'),
        ];
    }
}
