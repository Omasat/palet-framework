<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Workflow;

interface ConditionInterface
{
    public function evaluate(mixed $context): bool;
}
