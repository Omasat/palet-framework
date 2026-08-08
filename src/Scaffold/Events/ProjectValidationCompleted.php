<?php

declare(strict_types=1);

namespace Palet\Framework\Scaffold\Events;

class ProjectValidationCompleted
{
    public function __construct(
        public readonly string $targetPath
    ) {}
}
