<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Database\Query;

interface CompilerInterface
{
    /**
     * Compile a select query into SQL.
     */
    public function compileSelect(BuilderInterface $query): string;

    /**
     * Compile an insert statement into SQL.
     */
    public function compileInsert(BuilderInterface $query, array $values): string;

    /**
     * Compile an update statement into SQL.
     */
    public function compileUpdate(BuilderInterface $query, array $values): string;

    /**
     * Compile a delete statement into SQL.
     */
    public function compileDelete(BuilderInterface $query): string;
}
