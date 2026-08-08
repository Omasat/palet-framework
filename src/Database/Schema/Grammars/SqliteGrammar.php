<?php

declare(strict_types=1);

namespace Palet\Framework\Database\Schema\Grammars;

use Palet\Framework\Contracts\Database\Schema\BlueprintInterface;
use Palet\Framework\Contracts\Database\Schema\ColumnDefinitionInterface;

class SqliteGrammar extends Grammar
{
    protected function typeString(ColumnDefinitionInterface $column): string
    {
        return "varchar"; // SQLite ignores length mostly
    }
    
    protected function typeInteger(ColumnDefinitionInterface $column): string
    {
        return 'integer';
    }

    protected function typeBigInteger(ColumnDefinitionInterface $column): string
    {
        return 'integer'; // SQLite uses integer for primary keys
    }
    
    protected function modifyUnsigned(BlueprintInterface $blueprint, ColumnDefinitionInterface $column): string
    {
        return '';
    }
    
    protected function modifyIncrement(BlueprintInterface $blueprint, ColumnDefinitionInterface $column): string
    {
        return $column->get('autoIncrement') ? ' primary key autoincrement' : '';
    }
}
