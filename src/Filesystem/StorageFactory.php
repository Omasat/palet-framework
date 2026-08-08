<?php

declare(strict_types=1);

namespace Palet\Framework\Filesystem;

use Palet\Framework\Contracts\Filesystem\StorageDriverInterface;
use Palet\Framework\Filesystem\Drivers\LocalDriver;
use Palet\Framework\Filesystem\Drivers\PublicDriver;
use Palet\Framework\Filesystem\Drivers\TemporaryDriver;
use InvalidArgumentException;

class StorageFactory
{
    public function createDriver(string $name, array $config): StorageDriverInterface
    {
        $driver = $config['driver'] ?? 'local';

        return match ($driver) {
            'local' => new LocalDriver($config['root'] ?? 'storage/app'),
            'public' => new PublicDriver($config['root'] ?? 'public/storage', $config['url'] ?? '/storage'),
            'temp', 'temporary' => new TemporaryDriver($config['root'] ?? null),
            'memory' => new \Palet\Framework\Filesystem\Drivers\MemoryDriver(),
            'null' => new \Palet\Framework\Filesystem\Drivers\NullDriver(),
            default => throw new InvalidArgumentException("Driver [{$driver}] is not supported."),
        };
    }
}
