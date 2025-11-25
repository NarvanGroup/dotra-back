<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InstallmentResource\Pages;
use App\Models\Application;
use App\Models\Installment;
use BackedEnum;
use Filament\Actions;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use UnitEnum;

class InstallmentResource extends Resource
{
    protected static ?string $model = Installment::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-calendar-days';

    protected static string|UnitEnum|null $navigationGroup = 'Application Management';

    protected static ?int $navigationSort = 6;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                \Filament\Schemas\Components\Section::make('Installment Information')
                    ->schema([
                        \Filament\Forms\Components\Select::make('application_id')
                            ->label('Application')
                            ->relationship('application', 'id')
                            ->getOptionLabelFromRecordUsing(fn(Application $record
                            ) => "App: {$record->id} - {$record->customer->first_name} {$record->customer->last_name}"
                            )
                            ->searchable()
                            ->required()
                            ->native(false)
                            ->columnSpan(2)
                            ->preload(),

                        \Filament\Forms\Components\TextInput::make('installment_number')
                            ->label('Installment Number')
                            ->required()
                            ->numeric()
                            ->minValue(1)
                            ->columnSpan(1),

                        \Filament\Forms\Components\TextInput::make('amount')
                            ->label('Amount')
                            ->required()
                            ->numeric()
                            ->prefix('IRR')
                            ->minValue(0)
                            ->columnSpan(1),

                        \Filament\Forms\Components\DateTimePicker::make('due_date')
                            ->label('Due Date')
                            ->required()
                            ->native(false)
                            ->displayFormat('Y-m-d H:i')
                            ->columnSpan(1),

                        \Filament\Forms\Components\DateTimePicker::make('paid_at')
                            ->label('Paid At')
                            ->native(false)
                            ->displayFormat('Y-m-d H:i')
                            ->columnSpan(1),

                        \Filament\Forms\Components\Select::make('status')
                            ->options([
                                'pending'   => 'Pending',
                                'paid'      => 'Paid',
                                'overdue'   => 'Overdue',
                                'cancelled' => 'Cancelled',
                            ])
                            ->required()
                            ->native(false)
                            ->default('pending')
                            ->columnSpan(1),
                    ])
                    ->columns(2),
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

                Tables\Columns\TextColumn::make('application.customer.first_name')
                    ->label('Customer')
                    ->formatStateUsing(fn($record) => "{$record->application->customer->first_name} {$record->application->customer->last_name}"
                    )
                    ->searchable(['first_name', 'last_name'])
                    ->sortable(),

                Tables\Columns\TextColumn::make('application.vendor.name')
                    ->label('Vendor')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('installment_number')
                    ->label('#')
                    ->sortable()
                    ->alignCenter()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('amount')
                    ->money('IRR')
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('due_date')
                    ->label('Due Date')
                    ->dateTime('Y-m-d')
                    ->sortable()
                    ->color(fn($record) => $record->due_date < now() && $record->status !== 'paid' ? 'danger' : 'gray'
                    ),

                Tables\Columns\TextColumn::make('paid_at')
                    ->label('Paid At')
                    ->dateTime('Y-m-d H:i')
                    ->sortable()
                    ->toggleable()
                    ->placeholder('Not paid'),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn($state) => match ($state) {
                        'pending' => 'warning',
                        'paid' => 'success',
                        'overdue' => 'danger',
                        'cancelled' => 'gray',
                        default => 'info',
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('days_until_due')
                    ->label('Days Until Due')
                    ->state(fn($record) => $record->status === 'paid'
                        ? 'Paid'
                        : now()->diffInDays($record->due_date, false)
                    )
                    ->color(fn($record) => match (true) {
                        $record->status === 'paid' => 'success',
                        now()->diffInDays($record->due_date, false) < 0 => 'danger',
                        now()->diffInDays($record->due_date, false) <= 7 => 'warning',
                        default => 'info',
                    })
                    ->badge()
                    ->sortable(query: function ($query, string $direction) {
                        return $query->orderBy('due_date', $direction);
                    }),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending'   => 'Pending',
                        'paid'      => 'Paid',
                        'overdue'   => 'Overdue',
                        'cancelled' => 'Cancelled',
                    ])
                    ->native(false),

                Tables\Filters\Filter::make('overdue')
                    ->label('Overdue Only')
                    ->query(fn($query) => $query->where('due_date', '<', now())->where('status', '!=', 'paid')),

                Tables\Filters\Filter::make('unpaid')
                    ->label('Unpaid Only')
                    ->query(fn($query) => $query->whereNull('paid_at')),

                Tables\Filters\Filter::make('due_date')
                    ->form([
                        \Filament\Forms\Components\DatePicker::make('due_from')
                            ->native(false),
                        \Filament\Forms\Components\DatePicker::make('due_until')
                            ->native(false),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['due_from'], fn($q, $date) => $q->whereDate('due_date', '>=', $date))
                            ->when($data['due_until'], fn($q, $date) => $q->whereDate('due_date', '<=', $date));
                    }),
            ])
            ->actions([
                Actions\Action::make('mark_as_paid')
                    ->label('Mark as Paid')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn($record) => $record->status !== 'paid')
                    ->action(function ($record) {
                        $record->update([
                            'status'  => 'paid',
                            'paid_at' => now(),
                        ]);
                    }),
                Actions\ViewAction::make(),
                Actions\EditAction::make(),
                Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Actions\BulkActionGroup::make([
                    Actions\BulkAction::make('mark_as_paid')
                        ->label('Mark as Paid')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(function ($records) {
                            $records->each(function ($record) {
                                if ($record->status !== 'paid') {
                                    $record->update([
                                        'status'  => 'paid',
                                        'paid_at' => now(),
                                    ]);
                                }
                            });
                        }),
                    Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('due_date', 'asc');
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->schema([
                \Filament\Schemas\Components\Section::make('Installment Details')
                    ->schema([
                        \Filament\Infolists\Components\TextEntry::make('id')
                            ->label('Installment ID')
                            ->copyable(),
                        \Filament\Infolists\Components\TextEntry::make('installment_number')
                            ->label('Installment Number')
                            ->badge()
                            ->color('info'),
                        \Filament\Infolists\Components\TextEntry::make('status')
                            ->badge()
                            ->color(fn($state) => match ($state) {
                                'pending' => 'warning',
                                'paid' => 'success',
                                'overdue' => 'danger',
                                'cancelled' => 'gray',
                                default => 'info',
                            }),
                    ])
                    ->columns(3),

                \Filament\Schemas\Components\Section::make('Application Information')
                    ->schema([
                        \Filament\Infolists\Components\TextEntry::make('application.id')
                            ->label('Application ID')
                            ->copyable()
                            ->url(fn($record) => ApplicationResource::getUrl('view', ['record' => $record->application]))
                            ->color('primary'),
                        \Filament\Infolists\Components\TextEntry::make('application.customer.first_name')
                            ->label('Customer')
                            ->formatStateUsing(fn($record) => "{$record->application->customer->first_name} {$record->application->customer->last_name}"
                            )
                            ->url(fn($record) => CustomerResource::getUrl('view', ['record' => $record->application->customer]))
                            ->color('primary'),
                        \Filament\Infolists\Components\TextEntry::make('application.vendor.name')
                            ->label('Vendor')
                            ->url(fn($record) => VendorResource::getUrl('view', ['record' => $record->application->vendor]))
                            ->color('primary'),
                    ])
                    ->columns(3),

                \Filament\Schemas\Components\Section::make('Payment Information')
                    ->schema([
                        \Filament\Infolists\Components\TextEntry::make('amount')
                            ->money('IRR')
                            ->size('lg')
                            ->weight('bold'),
                        \Filament\Infolists\Components\TextEntry::make('due_date')
                            ->dateTime('Y-m-d H:i'),
                        \Filament\Infolists\Components\TextEntry::make('paid_at')
                            ->dateTime('Y-m-d H:i')
                            ->placeholder('Not paid yet'),
                    ])
                    ->columns(3),

                \Filament\Schemas\Components\Section::make('Payment Status')
                    ->schema([
                        \Filament\Infolists\Components\TextEntry::make('days_status')
                            ->label('Days Status')
                            ->state(fn($record) => match (true) {
                                $record->status === 'paid' => 'Paid on '.$record->paid_at->format('Y-m-d'),
                                now()->diffInDays($record->due_date, false) < 0 =>
                                    'Overdue by '.abs(now()->diffInDays($record->due_date, false)).' days',
                                now()->diffInDays($record->due_date, false) == 0 => 'Due today',
                                default => now()->diffInDays($record->due_date, false).' days remaining',
                            })
                            ->badge()
                            ->color(fn($record) => match (true) {
                                $record->status === 'paid' => 'success',
                                now()->diffInDays($record->due_date, false) < 0 => 'danger',
                                now()->diffInDays($record->due_date, false) <= 7 => 'warning',
                                default => 'info',
                            }),
                    ]),

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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListInstallments::route('/'),
            'create' => Pages\CreateInstallment::route('/create'),
            'view'   => Pages\ViewInstallment::route('/{record}'),
            'edit'   => Pages\EditInstallment::route('/{record}/edit'),
        ];
    }
}
