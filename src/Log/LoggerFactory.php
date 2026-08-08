<?php

declare(strict_types=1);

namespace Palet\Framework\Log;

use Palet\Framework\Contracts\Log\LogDriverInterface;
use Palet\Framework\Log\Drivers\FileDriver;
use Palet\Framework\Log\Drivers\DailyFileDriver;
use Palet\Framework\Log\Drivers\StackDriver;
use Palet\Framework\Log\Drivers\NullDriver;
use Palet\Framework\Log\Drivers\EmergencyDriver;
use InvalidArgumentException;

class LoggerFactory
{
    protected array $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function createDriver(string $channel): LogDriverInterface
    {
        if (!isset($this->config['channels'][$channel])) {
            throw new InvalidArgumentException("Log channel [{$channel}] is not defined.");
        }

        $config = $this->config['channels'][$channel];
        $driver = $config['driver'] ?? 'null';

        return match ($driver) {
            'single', 'file' => new FileDriver($config['path'] ?? 'palet.log'),
            'daily' => new DailyFileDriver($config['path'] ?? 'palet.log', $config['days'] ?? 7),
            'stack' => $this->createStackDriver($config),
            'null' => new NullDriver(),
            'emergency' => new EmergencyDriver(),
            default => throw new InvalidArgumentException("Log driver [{$driver}] is not supported."),
        };
    }

    protected function createStackDriver(array $config): StackDriver
    {
        $drivers = [];
        $channels = $config['channels'] ?? [];

        foreach ($channels as $channel) {
            $drivers[] = $this->createDriver($channel);
        }

        return new StackDriver($drivers);
    }
}
