<?php

declare(strict_types=1);

namespace Palet\Framework\Database\Migrations\Events;

class MigrationGenerated
{
    public function __construct(
        public readonly string $destinationPath,
        public readonly bool $isDryRun
    ) {}
}
