<?php

declare(strict_types=1);

namespace Palet\Framework\Support\ValueObjects;

use InvalidArgumentException;

final readonly class Money
{
    public function __construct(
        public int $amount,
        public string $currency = 'TRY'
    ) {
        if ($amount < 0) {
            throw new InvalidArgumentException("Amount cannot be negative: {$amount}");
        }
    }

    public function add(Money $other): self
    {
        if ($this->currency !== $other->currency) {
            throw new InvalidArgumentException("Cannot add different currencies.");
        }

        return new self($this->amount + $other->amount, $this->currency);
    }
    
    public function format(): string
    {
        return number_format($this->amount / 100, 2, ',', '.') . ' ' . $this->currency;
    }
}
