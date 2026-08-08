<?php

declare(strict_types=1);

namespace Palet\Framework\Environment;

use Palet\Framework\Contracts\Config\EnvironmentInterface;

class EnvRepository implements EnvironmentInterface
{
    /**
     * Parse edilmiş ve önbelleğe alınmış değişkenler.
     * @var array<string, mixed>
     */
    protected array $items = [];

    /**
     * @param array<string, mixed> $items
     */
    public function __construct(array $items = [])
    {
        $this->items = $items;
    }

    public function get(string $key, mixed $default = null): mixed
    {
        if ($this->has($key)) {
            return $this->items[$key];
        }

        // Eğer bellekte yoksa $_ENV ve $_SERVER'a bak
        $systemValue = $_ENV[$key] ?? $_SERVER[$key] ?? getenv($key);

        if ($systemValue !== false && $systemValue !== null) {
            return $systemValue;
        }

        return $default;
    }

    public function has(string $key): bool
    {
        return array_key_exists($key, $this->items);
    }

    /**
     * Set a given environment variable.
     */
    public function set(string $key, mixed $value): void
    {
        $this->items[$key] = $value;
        
        // Gerekirse global değişkenlere de yazılabilir,
        // ancak immutable/isolated bir yapı daha güvenlidir.
        $_ENV[$key] = $value;
        $_SERVER[$key] = $value;
    }

    /**
     * Get all environment variables.
     *
     * @return array<string, mixed>
     */
    public function all(): array
    {
        return $this->items;
    }
}
