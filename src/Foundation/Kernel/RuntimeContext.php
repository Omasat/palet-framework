<?php

declare(strict_types=1);

namespace Palet\Framework\Foundation\Kernel;

final readonly class RuntimeContext
{
    public function __construct(
        public string $environment = 'production',
        public bool $isRunningInConsole = false,
        public float $startTime = 0.0
    ) {
    }

    public static function detect(): self
    {
        return new self(
            $_ENV['APP_ENV'] ?? 'production',
            in_array(PHP_SAPI, ['cli', 'phpdbg'], true),
            $_SERVER['REQUEST_TIME_FLOAT'] ?? microtime(true)
        );
    }
}
