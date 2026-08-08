<?php

declare(strict_types=1);

namespace Palet\Framework\Filesystem;

use Palet\Framework\Contracts\Filesystem\StorageInterface;
use Palet\Framework\Contracts\Filesystem\FilesystemInterface;
use Palet\Framework\Contracts\Filesystem\StorageDriverInterface;
use InvalidArgumentException;

class StorageManager implements StorageInterface
{
    protected array $config;
    protected StorageFactory $factory;
    
    /**
     * @var array<string, StorageDriverInterface>
     */
    protected array $disks = [];

    public function __construct(array $config, ?StorageFactory $factory = null)
    {
        $this->config = $config;
        $this->factory = $factory ?? new StorageFactory();
    }

    public function disk(?string $name = null): FilesystemInterface
    {
        $name = $name ?: $this->getDefaultDriver();

        return $this->get($name);
    }

    protected function get(string $name): StorageDriverInterface
    {
        if (!isset($this->disks[$name])) {
            $this->disks[$name] = $this->resolve($name);
        }

        return $this->disks[$name];
    }

    protected function resolve(string $name): StorageDriverInterface
    {
        $config = $this->getConfig($name);

        return $this->factory->createDriver($name, $config);
    }

    protected function getConfig(string $name): array
    {
        if (!isset($this->config['disks'][$name])) {
            throw new InvalidArgumentException("Disk [{$name}] is not configured.");
        }

        return $this->config['disks'][$name];
    }

    public function getDefaultDriver(): string
    {
        return $this->config['default'] ?? 'local';
    }

    /**
     * Dynamically call the default driver instance.
     */
    public function __call(string $method, array $parameters)
    {
        return $this->disk()->$method(...$parameters);
    }
}
