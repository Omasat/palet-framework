<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Database\Migrations\Generator;

interface MigrationNamingStrategyInterface
{
    /**
     * Analyze the migration name and guess the table and action.
     *
     * @param string $name
     * @return array{table: string|null, create: bool}
     */
    public function analyze(string $name): array;
}
