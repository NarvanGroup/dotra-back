<?php

declare(strict_types=1);

namespace App\Enums\Concerns;

trait TranslatableEnum
{
    /**
     * Translate the enum value using its translation prefix and current locale.
     */
    public function label(): string
    {
        return trans($this->translationKey());
    }

    /**
     * Build the translation key using the provided prefix and enum value.
     */
    protected function translationKey(): string
    {
        return sprintf('%s.%s', static::translationPrefix(), $this->value);
    }

    /**
     * Retrieve enum values for validation rules and options.
     *
     * @return array<int, string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Each enum defines the prefix (e.g. "models.vendors.industries").
     */
    abstract public static function translationPrefix(): string;
}
