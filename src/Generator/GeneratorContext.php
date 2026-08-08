<?php

declare(strict_types=1);

namespace Palet\Framework\Generator;

class GeneratorContext
{
    public function __construct(
        public readonly string $stubPath,
        public readonly string $destinationPath,
        public readonly array $variables = [],
        public readonly bool $force = false,
        public readonly bool $dryRun = false
    ) {}
}
