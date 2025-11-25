<?php

namespace App\Models\Vendor;

use App\Enums\Concerns\TranslatableEnum;

enum VendorType: string
{
    use TranslatableEnum;

    case INDIVIDUAL = 'individual';
    case LEGAL = 'legal';

    public static function translationPrefix(): string
    {
        return 'models.vendors.types';
    }
}
