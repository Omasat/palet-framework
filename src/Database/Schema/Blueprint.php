<?php

declare(strict_types=1);

namespace Palet\Framework\Database\Schema;

use Palet\Framework\Contracts\Database\Schema\BlueprintInterface;
use Palet\Framework\Contracts\Database\Schema\CompilerInterface;
use Palet\Framework\Contracts\Database\Schema\ColumnDefinitionInterface;

class Blueprint implements BlueprintInterface
{
    protected string $table;
    protected array $columns = [];
    protected array $commands = [];

    public function __construct(string $table)
    {
        $this->table = $table;
    }

    public function getTable(): string
    {
        return $this->table;
    }

    public function getColumns(): array
    {
        return $this->columns;
    }

    public function getCommands(): array
    {
        return $this->commands;
    }

    public function create(): void
    {
        $this->addCommand('create');
    }

    public function drop(): void
    {
        $this->addCommand('drop');
    }

    public function dropIfExists(): void
    {
        $this->addCommand('dropIfExists');
    }

    protected function addCommand(string $name, array $parameters = []): void
    {
        $this->commands[] = array_merge(['name' => $name], $parameters);
    }

    public function addColumn(string $type, string $name, array $parameters = []): ColumnDefinitionInterface
    {
        $attributes = array_merge(['type' => $type, 'name' => $name], $parameters);
        $column = new ColumnDefinition($attributes);
        $this->columns[] = $column;

        return $column;
    }

    public function string(string $name, int $length = 255): ColumnDefinitionInterface
    {
        return $this->addColumn('string', $name, ['length' => $length]);
    }

    public function integer(string $name): ColumnDefinitionInterface
    {
        return $this->addColumn('integer', $name);
    }

    public function id(string $name = 'id'): ColumnDefinitionInterface
    {
        return $this->addColumn('bigInteger', $name, ['autoIncrement' => true, 'unsigned' => true]);
    }

    public function dropColumn(string|array $columns): void
    {
        $this->addCommand('dropColumn', ['columns' => (array) $columns]);
    }

    public function index(string|array $columns, ?string $name = null): void
    {
        $this->addCommand('index', ['columns' => (array) $columns, 'index' => $name]);
    }

    public function unique(string|array $columns, ?string $name = null): void
    {
        $this->addCommand('unique', ['columns' => (array) $columns, 'index' => $name]);
    }

    public function build(CompilerInterface $compiler): array
    {
        $statements = [];

        foreach ($this->commands as $command) {
            $method = 'compile' . ucfirst($command['name']);
            if (method_exists($compiler, $method)) {
                $sql = $compiler->$method($this, $command);
                if ($sql !== null) {
                    $statements[] = $sql;
                }
            }
        }

        return $statements;
    }
}
