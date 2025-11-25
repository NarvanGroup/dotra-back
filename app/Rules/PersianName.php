<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class PersianName implements Rule
{
    /**
     * Determine if the validation rule passes.
     */
    public function passes($attribute, $value): bool
    {
        if (! is_string($value)) {
            return false;
        }

        // Allow Persian & Arabic letters plus common separators (space, zero-width non-joiner, hyphen)
        return (bool) preg_match('/^[\p{Arabic}\x{200C}\x{200D}\s\-]+$/u', $value);
    }

    /**
     * Get the validation error message.
     */
    public function message(): string
    {
        return __('The :attribute may only contain Persian characters.');
    }
}

