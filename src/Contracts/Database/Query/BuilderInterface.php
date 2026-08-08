<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Database\Query;

interface BuilderInterface
{
    /**
     * Set the columns to be selected.
     */
    public function select(array|string $columns = ['*']): static;

    /**
     * Set the table which the query is targeting.
     */
    public function from(string $table, ?string $as = null): static;

    /**
     * Add a basic where clause to the query.
     */
    public function where(string|callable $column, mixed $operator = null, mixed $value = null, string $boolean = 'and'): static;

    /**
     * Add an "or where" clause to the query.
     */
    public function orWhere(string|callable $column, mixed $operator = null, mixed $value = null): static;

    /**
     * Add a join clause to the query.
     */
    public function join(string $table, string $first, string $operator = null, string $second = null, string $type = 'inner', bool $where = false): static;
    
    /**
     * Add a left join to the query.
     */
    public function leftJoin(string $table, string $first, string $operator = null, string $second = null): static;

    /**
     * Add an "order by" clause to the query.
     */
    public function orderBy(string $column, string $direction = 'asc'): static;

    /**
     * Set the "limit" value of the query.
     */
    public function limit(int $value): static;

    /**
     * Set the "offset" value of the query.
     */
    public function offset(int $value): static;
    
    /**
     * Get the SQL representation of the query.
     */
    public function toSql(): string;
    
    /**
     * Get the current query value bindings.
     */
    public function getBindings(): array;
}
