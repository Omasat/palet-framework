<?php

declare(strict_types=1);

namespace Palet\Framework\Database\Orm\Model;

use ArrayAccess;
use Countable;
use IteratorAggregate;
use ArrayIterator;
use Traversable;
use Palet\Framework\Contracts\Support\ArrayableInterface;
use Palet\Framework\Contracts\Support\JsonableInterface;

class ModelCollection implements ArrayAccess, Countable, IteratorAggregate, ArrayableInterface, JsonableInterface
{
    protected array $items = [];

    public function __construct(array $items = [])
    {
        $this->items = $items;
    }

    public function filter(callable $callback): static
    {
        return new static(array_filter($this->items, $callback));
    }

    public function map(callable $callback): static
    {
        $keys = array_keys($this->items);
        $items = array_map($callback, $this->items, $keys);

        return new static(array_combine($keys, $items));
    }

    public function first(callable $callback = null, mixed $default = null): mixed
    {
        if ($callback === null) {
            return empty($this->items) ? $default : reset($this->items);
        }

        foreach ($this->items as $key => $value) {
            if ($callback($value, $key)) {
                return $value;
            }
        }

        return $default;
    }
    
    public function toArray(): array
    {
        return array_map(function ($value) {
            return $value instanceof ArrayableInterface ? $value->toArray() : $value;
        }, $this->items);
    }
    
    public function toJson(int $options = 0): string
    {
        return json_encode($this->toArray(), $options);
    }

    // IteratorAggregate
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->items);
    }

    // Countable
    public function count(): int
    {
        return count($this->items);
    }

    // ArrayAccess
    public function offsetExists(mixed $offset): bool
    {
        return isset($this->items[$offset]);
    }

    public function offsetGet(mixed $offset): mixed
    {
        return $this->items[$offset];
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        if (is_null($offset)) {
            $this->items[] = $value;
        } else {
            $this->items[$offset] = $value;
        }
    }

    public function offsetUnset(mixed $offset): void
    {
        unset($this->items[$offset]);
    }
}
