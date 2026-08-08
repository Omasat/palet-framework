<?php

declare(strict_types=1);

namespace Palet\Framework\Config;

use Palet\Framework\Contracts\Config\ConfigInterface;

class ConfigRepository implements ConfigInterface
{
    /**
     * All of the configuration items.
     * @var array<string, mixed>
     */
    protected array $items = [];

    /**
     * Create a new configuration repository.
     *
     * @param array<string, mixed> $items
     */
    public function __construct(array $items = [])
    {
        $this->items = $items;
    }

    public function has(string $key): bool
    {
        if (array_key_exists($key, $this->items)) {
            return true;
        }

        $array = $this->items;
        $segments = explode('.', $key);

        foreach ($segments as $segment) {
            if (is_array($array) && array_key_exists($segment, $array)) {
                $array = $array[$segment];
            } else {
                return false;
            }
        }

        return true;
    }

    public function get(string $key, mixed $default = null): mixed
    {
        if (array_key_exists($key, $this->items)) {
            return $this->items[$key];
        }

        $array = $this->items;
        $segments = explode('.', $key);

        foreach ($segments as $segment) {
            if (is_array($array) && array_key_exists($segment, $array)) {
                $array = $array[$segment];
            } else {
                return $default;
            }
        }

        return $array;
    }

    public function set(string $key, mixed $value): void
    {
        $keys = explode('.', $key);
        $array = &$this->items;

        while (count($keys) > 1) {
            $keyPart = array_shift($keys);

            if (!isset($array[$keyPart]) || !is_array($array[$keyPart])) {
                $array[$keyPart] = [];
            }

            $array = &$array[$keyPart];
        }

        $array[array_shift($keys)] = $value;
    }

    /**
     * Get all of the configuration items.
     *
     * @return array<string, mixed>
     */
    public function all(): array
    {
        return $this->items;
    }
}
