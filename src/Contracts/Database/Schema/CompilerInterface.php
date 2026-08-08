<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Database\Schema;

interface CompilerInterface
{
    /**
     * Compile a create table command.
     */
    public function compileCreate(BlueprintInterface $blueprint): string;

    /**
     * Compile an add column command.
     */
    public function compileAdd(BlueprintInterface $blueprint, ColumnDefinitionInterface $column): string;

    /**
     * Compile a drop table command.
     */
    public function compileDrop(BlueprintInterface $blueprint): string;
    
    /**
     * Compile a drop table (if exists) command.
     */
    public function compileDropIfExists(BlueprintInterface $blueprint): string;
}
