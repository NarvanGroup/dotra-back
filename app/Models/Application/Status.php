<?php

declare(strict_types=1);

namespace App\Models\Application;

use App\Enums\Concerns\TranslatableEnum;

enum Status: string
{
    use TranslatableEnum;

    case CREATED_BY_VENDOR = 'created-by-vendor';
    case CREATED_BY_CUSTOMER = 'created-by-customer';
    case VENDOR_CONFIRMED = 'vendor-confirmed';
    case CUSTOMER_CONFIRMED = 'customer-confirmed';
    case APPROVED = 'approved';
    case IN_REPAYMENT = 'in-repayment';
    case OVERDUE = 'overdue';
    case REPAID = 'repaid';

    public static function translationPrefix(): string
    {
        return 'models.applications.statuses';
    }

    public static function fromMixed(string|self $value): self
    {
        return $value instanceof self ? $value : self::from($value);
    }

    public function canTransitionTo(self $to): bool
    {
        $allowed = match ($this) {
            self::CREATED_BY_VENDOR,
            self::CREATED_BY_CUSTOMER => [self::VENDOR_CONFIRMED],
            self::VENDOR_CONFIRMED => [self::CUSTOMER_CONFIRMED],
            self::CUSTOMER_CONFIRMED => [self::APPROVED],
            self::APPROVED => [self::IN_REPAYMENT],
            self::IN_REPAYMENT => [self::OVERDUE, self::REPAID],
            self::OVERDUE => [self::REPAID],
            self::REPAID => [],
        };

        return in_array($to, $allowed, true);
    }
}
