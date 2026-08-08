<?php

declare(strict_types=1);

namespace Palet\Framework\Generator\Events;

class FileGenerated
{
    public function __construct(
        public readonly string $destinationPath,
        public readonly bool $isDryRun
    ) {}
}
