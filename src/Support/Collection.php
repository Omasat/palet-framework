<?php

declare(strict_types=1);

namespace Palet\Framework\Support;

use ArrayAccess;
use Countable;
use IteratorAggregate;
use ArrayIterator;
use Closure;
use Traversable;

class Collection implements ArrayAccess, Countable, IteratorAggregate
{
    protected array $items = [];

    public function __construct(mixed $items = [])
    {
        $this->items = $this->getArrayableItems($items);
    }

    public static function make(mixed $items = []): static
    {
        return new static($items);
    }

    protected function getArrayableItems(mixed $items): array
    {
        if (is_array($items)) {
            return $items;
        } elseif ($items instanceof self) {
            return $items->all();
        } elseif ($items instanceof Traversable) {
            return iterator_to_array($items);
        }

        return (array) $items;
    }

    public function all(): array
    {
        return $this->items;
    }

    public function map(callable $callback): static
    {
        $keys = array_keys($this->items);
        $items = array_map($callback, $this->items, $keys);

        return new static(array_combine($keys, $items));
    }

    public function filter(?callable $callback = null): static
    {
        if ($callback) {
            return new static(array_filter($this->items, $callback, ARRAY_FILTER_USE_BOTH));
        }

        return new static(array_filter($this->items));
    }

    public function reject(callable $callback): static
    {
        return $this->filter(function ($item, $key) use ($callback) {
            return !$callback($item, $key);
        });
    }

    public function reduce(callable $callback, mixed $initial = null): mixed
    {
        return array_reduce($this->items, $callback, $initial);
    }

    public function first(?callable $callback = null, mixed $default = null): mixed
    {
        if (is_null($callback)) {
            if (empty($this->items)) {
                return is_callable($default) ? $default() : $default;
            }
            foreach ($this->items as $item) {
                return $item;
            }
        }

        foreach ($this->items as $key => $value) {
            if ($callback($value, $key)) {
                return $value;
            }
        }

        return is_callable($default) ? $default() : $default;
    }

    public function last(?callable $callback = null, mixed $default = null): mixed
    {
        if (is_null($callback)) {
            return empty($this->items) ? (is_callable($default) ? $default() : $default) : end($this->items);
        }

        return (new static(array_reverse($this->items, true)))->first($callback, $default);
    }

    public function pluck(string|array $value, string|array|null $key = null): static
    {
        $results = [];

        foreach ($this->items as $item) {
            $itemValue = is_array($item) ? Arr::get($item, $value) : (is_object($item) ? $item->$value : null);

            if (is_null($key)) {
                $results[] = $itemValue;
            } else {
                $itemKey = is_array($item) ? Arr::get($item, $key) : (is_object($item) ? $item->$key : null);
                $results[$itemKey] = $itemValue;
            }
        }

        return new static($results);
    }

    public function groupBy(string|callable $groupBy): static
    {
        $results = [];

        foreach ($this->items as $key => $value) {
            $groupKeys = is_callable($groupBy) ? $groupBy($value, $key) : (is_array($value) ? Arr::get($value, $groupBy) : (is_object($value) ? $value->$groupBy : null));

            if (!is_array($groupKeys)) {
                $groupKeys = [$groupKeys];
            }

            foreach ($groupKeys as $groupKey) {
                if (is_bool($groupKey)) {
                    $groupKey = (int) $groupKey;
                } elseif ($groupKey === null) {
                    $groupKey = '';
                }

                $results[$groupKey][] = $value;
            }
        }

        return new static(array_map(function ($items) {
            return new static($items);
        }, $results));
    }

    public function chunk(int $size): static
    {
        if ($size <= 0) {
            return new static([]);
        }

        $chunks = [];
        foreach (array_chunk($this->items, $size, true) as $chunk) {
            $chunks[] = new static($chunk);
        }

        return new static($chunks);
    }

    public function toArray(): array
    {
        return array_map(function ($value) {
            return $value instanceof self ? $value->toArray() : $value;
        }, $this->items);
    }

    public function count(): int
    {
        return count($this->items);
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->items);
    }

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
