<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Database\Migrations\Generator;

interface MigrationGeneratorInterface
{
    /**
     * Generate a new migration file.
     *
     * @param string $name
     * @param string $destinationDir
     * @param string|null $table
     * @param bool $create
     * @param bool $dryRun
     * @return string|null The generated path, or null if dry run
     */
    public function generate(string $name, string $destinationDir, ?string $table = null, bool $create = false, bool $dryRun = false): ?string;
}
