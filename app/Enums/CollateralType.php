<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum CollateralType: string implements HasLabel
{
    case CHECK = 'check';
    case PROMISSORY_NOTE = 'promissory_note';
    case OTHER = 'other';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::CHECK => 'Check',
            self::PROMISSORY_NOTE => 'Promissory Note',
            self::OTHER => 'Other',
        };
    }
}




