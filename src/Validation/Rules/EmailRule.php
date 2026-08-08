<?php

declare(strict_types=1);

namespace Palet\Framework\Validation\Rules;

use Palet\Framework\Contracts\Validation\RuleInterface;

class EmailRule implements RuleInterface
{
    public function passes(string $attribute, mixed $value): bool
    {
        if (empty($value)) return true; // Nullable behavior by default unless Required is present
        
        return filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
    }

    public function message(): string
    {
        return 'The :attribute must be a valid email address.';
    }
}
