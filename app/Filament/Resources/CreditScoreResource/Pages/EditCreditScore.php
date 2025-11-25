<?php

namespace App\Filament\Resources\CreditScoreResource\Pages;

use App\Filament\Resources\CreditScoreResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCreditScore extends EditRecord
{
    protected static string $resource = CreditScoreResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}

