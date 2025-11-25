<?php

declare(strict_types=1);

namespace App\Enums\CreditScore;

use App\Enums\Concerns\TranslatableEnum;

enum CreditScoreStatus: string
{
    use TranslatableEnum;

    case PENDING = 'pending';
    case PROCESSING = 'processing';
    case COMPLETED = 'completed';
    case FAILED = 'failed';

    public static function translationPrefix(): string
    {
        return 'models.credit_scores.statuses';
    }
}

