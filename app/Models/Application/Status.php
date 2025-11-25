<?php

declare(strict_types=1);

namespace App\Models\Application;

use App\Enums\Concerns\TranslatableEnum;

enum Status: string
{
    use TranslatableEnum;

    case TERMS_SUGGESTED = 'terms-suggested';
    case VENDOR_ADJUSTING = 'vendor-adjusting';
    case APPROVED = 'approved';
    case IN_REPAYMENT = 'in-repayment';
    case OVERDUE = 'overdue';
    case REPAID = 'repaid';

    public static function translationPrefix(): string
    {
        return 'models.applications.statuses';
    }
}


