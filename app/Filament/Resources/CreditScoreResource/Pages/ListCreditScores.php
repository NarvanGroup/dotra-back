<?php

namespace App\Filament\Resources\CreditScoreResource\Pages;

use App\Filament\Resources\CreditScoreResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCreditScores extends ListRecords
{
    protected static string $resource = CreditScoreResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

