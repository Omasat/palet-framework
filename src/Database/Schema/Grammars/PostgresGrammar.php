<?php

declare(strict_types=1);

namespace Palet\Framework\Database\Schema\Grammars;

use Palet\Framework\Contracts\Database\Schema\BlueprintInterface;
use Palet\Framework\Contracts\Database\Schema\ColumnDefinitionInterface;

class PostgresGrammar extends Grammar
{
    protected function typeString(ColumnDefinitionInterface $column): string
    {
        return "varchar({$column->get('length', 255)})";
    }
    
    protected function typeInteger(ColumnDefinitionInterface $column): string
    {
        return $column->get('autoIncrement') ? 'serial' : 'integer';
    }

    protected function typeBigInteger(ColumnDefinitionInterface $column): string
    {
        return $column->get('autoIncrement') ? 'bigserial' : 'bigint';
    }
    
    protected function modifyUnsigned(BlueprintInterface $blueprint, ColumnDefinitionInterface $column): string
    {
        // Postgres does not have unsigned types natively
        return '';
    }
    
    protected function modifyIncrement(BlueprintInterface $blueprint, ColumnDefinitionInterface $column): string
    {
        // The serial/bigserial type handles incrementing, just add primary key
        return $column->get('autoIncrement') ? ' primary key' : '';
    }
}
