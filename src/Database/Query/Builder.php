<?php

declare(strict_types=1);

namespace Palet\Framework\Database\Query;

use Palet\Framework\Contracts\Database\Query\BuilderInterface;
use Palet\Framework\Contracts\Database\Query\CompilerInterface;
use InvalidArgumentException;
use Closure;

class Builder implements BuilderInterface
{
    public ?CompilerInterface $compiler;

    public array $columns = [];
    public ?string $from = null;
    public array $wheres = [];
    public array $joins = [];
    public array $orders = [];
    public ?int $limit = null;
    public ?int $offset = null;
    
    public array $bindings = [
        'select' => [],
        'join'   => [],
        'where'  => [],
        'order'  => [],
    ];

    public function __construct(?CompilerInterface $compiler = null)
    {
        $this->compiler = $compiler;
    }

    public function select(array|string $columns = ['*']): static
    {
        $clone = clone $this;
        $clone->columns = is_array($columns) ? $columns : func_get_args();
        return $clone;
    }

    public function from(string $table, ?string $as = null): static
    {
        $clone = clone $this;
        $clone->from = $as ? "{$table} as {$as}" : $table;
        return $clone;
    }

    public function where(string|callable $column, mixed $operator = null, mixed $value = null, string $boolean = 'and'): static
    {
        $clone = clone $this;

        if ($column instanceof Closure) {
            $query = new self($clone->compiler);
            $query = $column($query) ?? $query;
            $clone->wheres[] = [
                'type' => 'Nested',
                'query' => $query,
                'boolean' => $boolean,
            ];
            $clone->addBinding($query->getRawBindings()['where'], 'where');
            return $clone;
        }

        if (func_num_args() === 2) {
            $value = $operator;
            $operator = '=';
        }

        $clone->wheres[] = [
            'type' => 'Basic',
            'column' => $column,
            'operator' => $operator,
            'value' => $value,
            'boolean' => $boolean,
        ];

        if (!$value instanceof Expression) {
            $clone->addBinding($value, 'where');
        }

        return $clone;
    }

    public function orWhere(string|callable $column, mixed $operator = null, mixed $value = null): static
    {
        return $this->where($column, $operator, $value, 'or');
    }

    public function join(string $table, string $first, string $operator = null, string $second = null, string $type = 'inner', bool $where = false): static
    {
        $clone = clone $this;
        $clone->joins[] = [
            'table' => $table,
            'first' => $first,
            'operator' => $operator ?? '=',
            'second' => $second,
            'type' => $type,
            'where' => $where,
        ];
        return $clone;
    }

    public function leftJoin(string $table, string $first, string $operator = null, string $second = null): static
    {
        return $this->join($table, $first, $operator, $second, 'left');
    }

    public function orderBy(string $column, string $direction = 'asc'): static
    {
        $clone = clone $this;
        $clone->orders[] = [
            'column' => $column,
            'direction' => strtolower($direction) === 'asc' ? 'asc' : 'desc',
        ];
        return $clone;
    }

    public function limit(int $value): static
    {
        $clone = clone $this;
        $clone->limit = max(0, $value);
        return $clone;
    }

    public function offset(int $value): static
    {
        $clone = clone $this;
        $clone->offset = max(0, $value);
        return $clone;
    }

    public function addBinding(mixed $value, string $type = 'where'): static
    {
        if (!array_key_exists($type, $this->bindings)) {
            throw new InvalidArgumentException("Invalid binding type: {$type}.");
        }

        if (is_array($value)) {
            $this->bindings[$type] = array_merge($this->bindings[$type], $value);
        } else {
            $this->bindings[$type][] = $value;
        }

        return $this;
    }

    public function getBindings(): array
    {
        $result = [];
        foreach ($this->bindings as $type => $bindings) {
            foreach ($bindings as $binding) {
                $result[] = $binding;
            }
        }
        return $result;
    }

    public function getRawBindings(): array
    {
        return $this->bindings;
    }

    public function toSql(): string
    {
        if (!$this->compiler) {
            throw new \RuntimeException('No compiler set for the query builder.');
        }

        return $this->compiler->compileSelect($this);
    }
}
