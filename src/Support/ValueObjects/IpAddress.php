<?php

declare(strict_types=1);

namespace Palet\Framework\Support\ValueObjects;

use InvalidArgumentException;
use Stringable;

final readonly class IpAddress implements Stringable
{
    public function __construct(
        public string $value
    ) {
        if (!filter_var($value, FILTER_VALIDATE_IP)) {
            throw new InvalidArgumentException("Invalid IP address: {$value}");
        }
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
