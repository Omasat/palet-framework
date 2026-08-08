<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Database\Schema;

use Closure;

interface BuilderInterface
{
    /**
     * Create a new table on the schema.
     */
    public function create(string $table, Closure $callback): void;

    /**
     * Modify a table on the schema.
     */
    public function table(string $table, Closure $callback): void;

    /**
     * Drop a table from the schema.
     */
    public function drop(string $table): void;

    /**
     * Drop a table from the schema if it exists.
     */
    public function dropIfExists(string $table): void;

    /**
     * Rename a table on the schema.
     */
    public function rename(string $from, string $to): void;
}
