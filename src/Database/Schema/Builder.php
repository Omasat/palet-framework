<?php

declare(strict_types=1);

namespace Palet\Framework\Database\Schema;

use Palet\Framework\Contracts\Database\Schema\BuilderInterface;
use Palet\Framework\Contracts\Database\Schema\CompilerInterface;
use Closure;

class Builder implements BuilderInterface
{
    protected CompilerInterface $compiler;
    
    // Using a callback to pretend executing DDL commands since we don't have
    // an active connection execution engine defined for Schema Builder yet.
    protected ?Closure $resolver = null; 

    public function __construct(CompilerInterface $compiler, ?Closure $resolver = null)
    {
        $this->compiler = $compiler;
        $this->resolver = $resolver;
    }

    protected function createBlueprint(string $table, ?Closure $callback = null): Blueprint
    {
        $blueprint = new Blueprint($table);

        if ($callback) {
            $callback($blueprint);
        }

        return $blueprint;
    }

    protected function build(Blueprint $blueprint): void
    {
        $statements = $blueprint->build($this->compiler);
        
        if ($this->resolver) {
            call_user_func($this->resolver, $statements);
        }
    }

    public function create(string $table, Closure $callback): void
    {
        $blueprint = $this->createBlueprint($table, $callback);
        $blueprint->create();
        
        $this->build($blueprint);
    }

    public function table(string $table, Closure $callback): void
    {
        $blueprint = $this->createBlueprint($table, $callback);
        $this->build($blueprint);
    }

    public function drop(string $table): void
    {
        $blueprint = $this->createBlueprint($table);
        $blueprint->drop();
        
        $this->build($blueprint);
    }

    public function dropIfExists(string $table): void
    {
        $blueprint = $this->createBlueprint($table);
        $blueprint->dropIfExists();
        
        $this->build($blueprint);
    }

    public function rename(string $from, string $to): void
    {
        $blueprint = $this->createBlueprint($from);
        $blueprint->addCommand('rename', ['to' => $to]); // Internal call, ignoring interface constraint for simplicity
        
        $this->build($blueprint);
    }
}
