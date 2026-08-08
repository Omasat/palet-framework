<?php

declare(strict_types=1);

namespace Palet\Framework\Foundation;

/**
 * Çalışma zamanı (Runtime) bilgilerini sağlar.
 */
final class Runtime
{
    /**
     * Get the current PHP version.
     */
    public static function phpVersion(): string
    {
        return PHP_VERSION;
    }

    /**
     * Get the operating system name.
     */
    public static function os(): string
    {
        return PHP_OS_FAMILY;
    }

    /**
     * Get the current memory usage in bytes.
     */
    public static function memoryUsage(): int
    {
        return memory_get_usage();
    }

    /**
     * Get the peak memory usage in bytes.
     */
    public static function peakMemoryUsage(): int
    {
        return memory_get_peak_usage();
    }

    /**
     * Determine if the application is running in CLI mode.
     */
    public static function isCli(): bool
    {
        return \in_array(PHP_SAPI, ['cli', 'phpdbg', 'embed'], true);
    }
}
