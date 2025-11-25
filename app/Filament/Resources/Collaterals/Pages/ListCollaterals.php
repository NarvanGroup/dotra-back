<?php

namespace App\Filament\Resources\Collaterals\Pages;

use App\Filament\Resources\Collaterals\CollateralResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCollaterals extends ListRecords
{
    protected static string $resource = CollateralResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
