<?php

declare(strict_types=1);

namespace Palet\Framework\Database\Schema\Grammars;

use Palet\Framework\Contracts\Database\Schema\CompilerInterface;
use Palet\Framework\Contracts\Database\Schema\BlueprintInterface;
use Palet\Framework\Contracts\Database\Schema\ColumnDefinitionInterface;
use Palet\Framework\Database\Schema\Blueprint;
use Palet\Framework\Database\Schema\ColumnDefinition;

abstract class Grammar implements CompilerInterface
{
    public function compileCreate(BlueprintInterface $blueprint): string
    {
        $columns = $this->getColumns($blueprint);
        
        // Wrap table name
        $table = $this->wrapTable($blueprint->getTable());
        
        $sql = "create table {$table} (" . implode(', ', $columns) . ")";
        
        return $sql;
    }

    public function compileAdd(BlueprintInterface $blueprint, ColumnDefinitionInterface $column): string
    {
        return '';
    }

    public function compileDrop(BlueprintInterface $blueprint): string
    {
        return 'drop table ' . $this->wrapTable($blueprint->getTable());
    }
    
    public function compileDropIfExists(BlueprintInterface $blueprint): string
    {
        return 'drop table if exists ' . $this->wrapTable($blueprint->getTable());
    }

    protected function getColumns(BlueprintInterface $blueprint): array
    {
        $columns = [];
        
        foreach ($blueprint->getColumns() as $column) {
            $sql = $this->wrap($column->get('name')) . ' ' . $this->getType($column);
            $sql = $this->addModifiers($sql, $blueprint, $column);
            $columns[] = $sql;
        }
        
        return $columns;
    }
    
    protected function getType(ColumnDefinitionInterface $column): string
    {
        $type = $column->get('type');
        $method = 'type' . ucfirst($type);
        
        if (method_exists($this, $method)) {
            return $this->$method($column);
        }
        
        return $type;
    }
    
    protected function addModifiers(string $sql, BlueprintInterface $blueprint, ColumnDefinitionInterface $column): string
    {
        foreach ($this->getModifiers() as $modifier) {
            $method = "modify{$modifier}";
            if (method_exists($this, $method)) {
                $sql .= $this->{$method}($blueprint, $column);
            }
        }
        
        return $sql;
    }
    
    protected function getModifiers(): array
    {
        return ['Unsigned', 'Nullable', 'Default', 'Increment'];
    }
    
    protected function modifyNullable(BlueprintInterface $blueprint, ColumnDefinitionInterface $column): string
    {
        return $column->get('nullable') ? ' null' : ' not null';
    }

    protected function modifyDefault(BlueprintInterface $blueprint, ColumnDefinitionInterface $column): string
    {
        if ($column->get('default') !== null) {
            return ' default ' . $this->getDefaultValue($column->get('default'));
        }
        return '';
    }
    
    protected function getDefaultValue(mixed $value): string
    {
        if (is_bool($value)) {
            return $value ? '1' : '0'; // Generic boolean handling
        }
        
        if (is_string($value)) {
            return "'" . str_replace("'", "''", $value) . "'";
        }
        
        return (string) $value;
    }

    public function wrapTable(string $table): string
    {
        return $this->wrap($table);
    }
    
    public function wrap(string $value): string
    {
        return '"' . str_replace('"', '""', $value) . '"';
    }
}
