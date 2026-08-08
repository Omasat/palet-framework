<?php

declare(strict_types=1);

namespace Palet\Framework\Validation\Rules;

use Palet\Framework\Contracts\Validation\RuleInterface;

class MinRule implements RuleInterface
{
    protected float $min;

    public function __construct(string|float|int $min)
    {
        $this->min = (float) $min;
    }

    public function passes(string $attribute, mixed $value): bool
    {
        if (empty($value)) return true;
        
        if (is_numeric($value)) {
            return $value >= $this->min;
        }
        
        if (is_string($value)) {
            return mb_strlen($value) >= $this->min;
        }
        
        if (is_array($value)) {
            return count($value) >= $this->min;
        }
        
        return false;
    }

    public function message(): string
    {
        return "The :attribute must be at least {$this->min}.";
    }
}
