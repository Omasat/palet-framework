<?php

declare(strict_types=1);

namespace Palet\Framework\Database\Schema\Grammars;

use Palet\Framework\Contracts\Database\Schema\BlueprintInterface;
use Palet\Framework\Contracts\Database\Schema\ColumnDefinitionInterface;

class MysqlGrammar extends Grammar
{
    public function wrap(string $value): string
    {
        return '`' . str_replace('`', '``', $value) . '`';
    }
    
    public function compileCreate(BlueprintInterface $blueprint): string
    {
        $sql = parent::compileCreate($blueprint);
        return $sql . ' default character set utf8mb4';
    }

    protected function typeString(ColumnDefinitionInterface $column): string
    {
        return "varchar({$column->get('length', 255)})";
    }
    
    protected function typeInteger(ColumnDefinitionInterface $column): string
    {
        return 'int';
    }

    protected function typeBigInteger(ColumnDefinitionInterface $column): string
    {
        return 'bigint';
    }
    
    protected function modifyUnsigned(BlueprintInterface $blueprint, ColumnDefinitionInterface $column): string
    {
        return $column->get('unsigned') ? ' unsigned' : '';
    }

    protected function modifyIncrement(BlueprintInterface $blueprint, ColumnDefinitionInterface $column): string
    {
        return $column->get('autoIncrement') ? ' auto_increment primary key' : '';
    }
}
