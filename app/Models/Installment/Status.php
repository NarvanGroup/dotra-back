<?php

declare(strict_types=1);

namespace App\Models\Installment;

use App\Enums\Concerns\TranslatableEnum;

enum Status: string
{
    use TranslatableEnum;

    case PENDING = 'pending';
    case PAID = 'paid';
    case OVERDUE = 'overdue';

    public static function translationPrefix(): string
    {
        return 'models.installments.statuses';
    }

    public static function fromMixed(string|self $value): self
    {
        return $value instanceof self ? $value : self::from($value);
    }
}

