<?php

declare(strict_types=1);

namespace Palet\Framework\Generator\Scaffold\Events;

class ScaffoldStarted
{
    public function __construct(
        public readonly string $blueprintName,
        public readonly array $executionOrder
    ) {}
}
