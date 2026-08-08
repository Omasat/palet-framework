<?php

declare(strict_types=1);

namespace Palet\Framework\Database\Migrations\Events;

class MigrationGenerationFailed
{
    public function __construct(
        public readonly string $name,
        public readonly \Throwable $exception
    ) {}
}
