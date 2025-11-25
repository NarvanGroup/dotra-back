<?php

namespace App\Filament\Resources\Collaterals\Pages;

use App\Filament\Resources\Collaterals\CollateralResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditCollateral extends EditRecord
{
    protected static string $resource = CollateralResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
