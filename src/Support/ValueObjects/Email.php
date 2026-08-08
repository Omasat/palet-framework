<?php

declare(strict_types=1);

namespace Palet\Framework\Support\ValueObjects;

use InvalidArgumentException;
use Stringable;

final readonly class Email implements Stringable
{
    public function __construct(
        public string $value
    ) {
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException("Invalid email address: {$value}");
        }
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
