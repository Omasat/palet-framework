<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Database\Schema;

interface BlueprintInterface
{
    /**
     * Build the blueprint and return the SQL statements.
     */
    public function build(CompilerInterface $compiler): array;

    /**
     * Add a new column to the blueprint.
     */
    public function addColumn(string $type, string $name, array $parameters = []): ColumnDefinitionInterface;

    /**
     * Indicate that the given columns should be dropped.
     */
    public function dropColumn(string|array $columns): void;

    /**
     * Add an index to the blueprint.
     */
    public function index(string|array $columns, ?string $name = null): void;

    /**
     * Add a unique index to the blueprint.
     */
    public function unique(string|array $columns, ?string $name = null): void;
}
