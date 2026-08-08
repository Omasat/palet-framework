<?php

declare(strict_types=1);

namespace Palet\Framework\Validation\Rules;

use Palet\Framework\Contracts\Validation\RuleInterface;

class RequiredRule implements RuleInterface
{
    public function passes(string $attribute, mixed $value): bool
    {
        if (is_null($value)) {
            return false;
        } elseif (is_string($value) && trim($value) === '') {
            return false;
        } elseif (is_array($value) && count($value) < 1) {
            return false;
        }

        return true;
    }

    public function message(): string
    {
        return 'The :attribute field is required.';
    }
}
