<?php

namespace App\Models\Vendor;

enum VendorType: string
{
    case INDIVIDUAL = 'individual';
    case LEGAL = 'legal';

    /**
     * Retrieve enum values for validation rules.
     *
     * @return array<int, string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}

