<?php

declare(strict_types=1);

namespace Palet\Framework\Database\Query\Grammars;

use Palet\Framework\Contracts\Database\Query\CompilerInterface;
use Palet\Framework\Contracts\Database\Query\BuilderInterface;
use Palet\Framework\Database\Query\Builder;
use Palet\Framework\Database\Query\Expression;

abstract class Grammar implements CompilerInterface
{
    /**
     * The grammar specific table prefix.
     */
    protected string $tablePrefix = '';

    public function compileSelect(BuilderInterface $query): string
    {
        if (empty($query->columns)) {
            $query->columns = ['*'];
        }

        $sql = trim($this->concatenate([
            $this->compileColumns($query, $query->columns),
            $this->compileFrom($query, $query->from),
            $this->compileJoins($query, $query->joins),
            $this->compileWheres($query, $query->wheres),
            $this->compileOrders($query, $query->orders),
            $this->compileLimit($query, $query->limit),
            $this->compileOffset($query, $query->offset),
        ]));

        return $sql;
    }

    protected function concatenate(array $segments): string
    {
        return implode(' ', array_filter($segments, fn ($value) => (string) $value !== ''));
    }

    protected function compileColumns(Builder $query, array $columns): string
    {
        return 'select ' . $this->columnize($columns);
    }

    protected function compileFrom(Builder $query, ?string $table): string
    {
        if (is_null($table)) {
            return '';
        }
        return 'from ' . $this->wrapTable($table);
    }

    protected function compileJoins(Builder $query, array $joins): string
    {
        if (empty($joins)) {
            return '';
        }

        $sql = [];

        foreach ($joins as $join) {
            $table = $this->wrapTable($join['table']);
            $first = $this->wrap($join['first']);
            $second = $this->wrap($join['second']);
            $type = strtoupper($join['type']);
            
            $sql[] = "{$type} JOIN {$table} ON {$first} {$join['operator']} {$second}";
        }

        return implode(' ', $sql);
    }

    protected function compileWheres(Builder $query, array $wheres): string
    {
        if (empty($wheres)) {
            return '';
        }

        $sql = [];

        foreach ($wheres as $where) {
            $sql[] = $where['boolean'] . ' ' . $this->{"compileWhere{$where['type']}"}($query, $where);
        }

        if (count($sql) > 0) {
            $sql = implode(' ', $sql);
            return 'where ' . preg_replace('/and |or /i', '', $sql, 1);
        }

        return '';
    }

    protected function compileWhereBasic(Builder $query, array $where): string
    {
        $value = $where['value'] instanceof Expression ? $where['value']->getValue() : '?';
        return $this->wrap($where['column']) . ' ' . $where['operator'] . ' ' . $value;
    }

    protected function compileWhereNested(Builder $query, array $where): string
    {
        $nestedSql = substr($this->compileWheres($query, $where['query']->wheres), 6); // remove "where "
        return '(' . $nestedSql . ')';
    }

    protected function compileOrders(Builder $query, array $orders): string
    {
        if (empty($orders)) {
            return '';
        }

        $sql = [];

        foreach ($orders as $order) {
            $sql[] = $this->wrap($order['column']) . ' ' . $order['direction'];
        }

        return 'order by ' . implode(', ', $sql);
    }

    protected function compileLimit(Builder $query, ?int $limit): string
    {
        if (is_null($limit)) {
            return '';
        }
        return 'limit ' . $limit;
    }

    protected function compileOffset(Builder $query, ?int $offset): string
    {
        if (is_null($offset)) {
            return '';
        }
        return 'offset ' . $offset;
    }

    public function compileInsert(BuilderInterface $query, array $values): string
    {
        $table = $this->wrapTable($query->from);
        
        if (empty($values)) {
            return "insert into {$table} default values";
        }
        
        $columns = $this->columnize(array_keys($values));
        $parameters = implode(', ', array_fill(0, count($values), '?'));
        
        return "insert into {$table} ({$columns}) values ({$parameters})";
    }

    public function compileUpdate(BuilderInterface $query, array $values): string
    {
        $table = $this->wrapTable($query->from);
        
        $columns = [];
        foreach ($values as $key => $value) {
            $columns[] = $this->wrap($key) . ' = ' . ($value instanceof Expression ? $value->getValue() : '?');
        }
        $columns = implode(', ', $columns);
        
        $wheres = $this->compileWheres($query, $query->wheres);
        
        return trim("update {$table} set {$columns} {$wheres}");
    }

    public function compileDelete(BuilderInterface $query): string
    {
        $table = $this->wrapTable($query->from);
        $wheres = $this->compileWheres($query, $query->wheres);
        
        return trim("delete from {$table} {$wheres}");
    }

    public function columnize(array $columns): string
    {
        return implode(', ', array_map([$this, 'wrap'], $columns));
    }

    public function wrap(string|Expression $value): string
    {
        if ($value instanceof Expression) {
            return (string) $value->getValue();
        }

        if (str_contains(strtolower($value), ' as ')) {
            return $this->wrapAliasedValue($value);
        }

        return $this->wrapSegments(explode('.', $value));
    }

    protected function wrapAliasedValue(string $value): string
    {
        $segments = preg_split('/\s+as\s+/i', $value);
        return $this->wrap($segments[0]) . ' as ' . $this->wrapValue($segments[1]);
    }

    protected function wrapSegments(array $segments): string
    {
        return implode('.', array_map([$this, 'wrapValue'], $segments));
    }

    protected function wrapValue(string $value): string
    {
        if ($value === '*') {
            return $value;
        }

        return '"' . str_replace('"', '""', $value) . '"';
    }

    public function wrapTable(string|Expression $table): string
    {
        if ($table instanceof Expression) {
            return (string) $table->getValue();
        }

        return $this->wrap($this->tablePrefix . $table);
    }
}
