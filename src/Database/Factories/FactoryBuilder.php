<?php

declare(strict_types=1);

namespace Palet\Framework\Database\Factories;

use Palet\Framework\Contracts\Database\Factories\FactoryBuilderInterface;
use Palet\Framework\Contracts\Database\Factories\FactoryInterface;
use Closure;

class FactoryBuilder implements FactoryBuilderInterface
{
    protected string $factoryClass;
    protected int $count = 1;
    protected array $states = [];
    protected array $sequences = [];
    
    protected array $afterMake = [];
    protected array $afterCreate = [];

    public function __construct(string $factoryClass)
    {
        $this->factoryClass = $factoryClass;
    }

    public function count(int $count): static
    {
        $clone = clone $this;
        $clone->count = $count;
        return $clone;
    }

    public function state(callable|array|string $state): static
    {
        $clone = clone $this;
        $clone->states[] = $state;
        return $clone;
    }

    public function sequence(array ...$sequence): static
    {
        $clone = clone $this;
        $clone->sequences = array_merge($clone->sequences, $sequence);
        return $clone;
    }

    public function afterMake(Closure $callback): static
    {
        $clone = clone $this;
        $clone->afterMake[] = $callback;
        return $clone;
    }

    public function make(array $attributes = []): array
    {
        $factory = new $this->factoryClass;
        $results = [];

        for ($i = 0; $i < $this->count; $i++) {
            $data = $factory->definition();

            // Apply Sequences
            if (!empty($this->sequences)) {
                $sequenceData = $this->sequences[$i % count($this->sequences)];
                $data = array_merge($data, $sequenceData);
            }

            // Apply States
            foreach ($this->states as $state) {
                if (is_callable($state)) {
                    $data = array_merge($data, $state($data));
                } elseif (is_array($state)) {
                    $data = array_merge($data, $state);
                } elseif (is_string($state) && method_exists($factory, $state)) {
                    $data = array_merge($data, $factory->{$state}($data));
                }
            }
            
            // Apply Manual Attributes
            $data = array_merge($data, $attributes);
            
            // Apply afterMake hooks
            foreach ($this->afterMake as $hook) {
                $hook($data);
            }

            $results[] = $data;
        }

        return $results;
    }

    public function create(array $attributes = []): array
    {
        $results = $this->make($attributes);
        
        // Mocking the ORM insertion process
        // In reality, this would insert into DB and return Models
        
        // Apply afterCreate hooks
        foreach ($this->afterCreate as $hook) {
            foreach ($results as &$result) {
                $hook($result);
            }
        }
        
        return $results;
    }
}
