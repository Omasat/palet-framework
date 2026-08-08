<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Database\Factories;

use Closure;

interface FactoryBuilderInterface
{
    /**
     * Set the number of models you wish to create / make.
     */
    public function count(int $count): static;

    /**
     * Add a new state transformation to the model definition.
     */
    public function state(callable|array|string $state): static;

    /**
     * Add a new sequence to the model definition.
     */
    public function sequence(array ...$sequence): static;

    /**
     * Add a callback to run after making a model.
     */
    public function afterMake(Closure $callback): static;

    /**
     * Create a collection of models and persist them to the database.
     */
    public function create(array $attributes = []): array;

    /**
     * Create a collection of models without persisting them to the database.
     */
    public function make(array $attributes = []): array;
}
