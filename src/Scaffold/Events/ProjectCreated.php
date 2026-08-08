<?php

declare(strict_types=1);

namespace Palet\Framework\Scaffold\Events;

class ProjectCreated
{
    public function __construct(
        public readonly string $targetPath,
        public readonly string $templateName
    ) {}
}
