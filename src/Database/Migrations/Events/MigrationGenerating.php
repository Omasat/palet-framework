<?php

declare(strict_types=1);

namespace Palet\Framework\Database\Migrations\Events;

class MigrationGenerating
{
    public function __construct(
        public readonly string $name,
        public readonly ?string $tableName,
        public readonly bool $isCreate
    ) {}
}
