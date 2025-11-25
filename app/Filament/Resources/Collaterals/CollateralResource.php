<?php

namespace App\Filament\Resources\Collaterals;

use App\Filament\Resources\Collaterals\Pages\CreateCollateral;
use App\Filament\Resources\Collaterals\Pages\EditCollateral;
use App\Filament\Resources\Collaterals\Pages\ListCollaterals;
use App\Filament\Resources\Collaterals\Schemas\CollateralForm;
use App\Filament\Resources\Collaterals\Tables\CollateralsTable;
use App\Models\Collateral;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class CollateralResource extends Resource
{
    protected static ?string $model = Collateral::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|\UnitEnum|null $navigationGroup = 'Collateral Management';

    protected static ?string $recordTitleAttribute = 'type';

    public static function getRecordTitle($record): string
    {
        return $record->type?->getLabel() ?? '';
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['application.customer', 'application.vendor', 'vendor', 'customer']);
    }

    public static function form(Schema $schema): Schema
    {
        return CollateralForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CollateralsTable::configure($table);
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
            'index'  => ListCollaterals::route('/'),
            'create' => CreateCollateral::route('/create'),
            'edit'   => EditCollateral::route('/{record}/edit'),
        ];
    }
}
