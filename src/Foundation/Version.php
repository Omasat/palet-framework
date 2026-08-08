<?php

declare(strict_types=1);

namespace Palet\Framework\Foundation;

/**
 * Framework sürüm ve build bilgilerini tutar.
 */
final class Version
{
    public const VERSION = '0.1.0';
    public const BUILD = '1000';
    public const RELEASE_CHANNEL = 'alpha';
    public const API_VERSION = '1.0';
    public const MINIMUM_PHP_VERSION = '8.2.0';

    /**
     * Get the full framework version.
     */
    public static function getVersion(): string
    {
        return self::VERSION;
    }

    /**
     * Get the build number.
     */
    public static function getBuild(): string
    {
        return self::BUILD;
    }

    /**
     * Get the release channel.
     */
    public static function getReleaseChannel(): string
    {
        return self::RELEASE_CHANNEL;
    }

    /**
     * Get the minimum supported PHP version.
     */
    public static function getMinimumPhpVersion(): string
    {
        return self::MINIMUM_PHP_VERSION;
    }
}
