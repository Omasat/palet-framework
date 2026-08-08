<?php

declare(strict_types=1);

namespace Palet\Framework\Validation\Rules;

use Palet\Framework\Contracts\Validation\RuleInterface;

class MaxRule implements RuleInterface
{
    protected float $max;

    public function __construct(string|float|int $max)
    {
        $this->max = (float) $max;
    }

    public function passes(string $attribute, mixed $value): bool
    {
        if (empty($value)) return true;
        
        if (is_numeric($value)) {
            return $value <= $this->max;
        }
        
        if (is_string($value)) {
            return mb_strlen($value) <= $this->max;
        }
        
        if (is_array($value)) {
            return count($value) <= $this->max;
        }
        
        return false;
    }

    public function message(): string
    {
        return "The :attribute may not be greater than {$this->max}.";
    }
}
