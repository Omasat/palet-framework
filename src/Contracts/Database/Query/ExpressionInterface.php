<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Database\Query;

interface ExpressionInterface
{
    /**
     * Get the value of the expression.
     */
    public function getValue(): string|int|float;
}
