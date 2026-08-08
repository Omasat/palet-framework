<?php

declare(strict_types=1);

namespace Palet\Framework\Generator\Scaffold\Events;

class ScaffoldFailed
{
    public function __construct(
        public readonly string $blueprintName,
        public readonly string $error
    ) {}
}
