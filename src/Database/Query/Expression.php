<?php

declare(strict_types=1);

namespace Palet\Framework\Database\Query;

use Palet\Framework\Contracts\Database\Query\ExpressionInterface;

class Expression implements ExpressionInterface
{
    protected string|int|float $value;

    public function __construct(string|int|float $value)
    {
        $this->value = $value;
    }

    public function getValue(): string|int|float
    {
        return $this->value;
    }
}
