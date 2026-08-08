<?php

declare(strict_types=1);

namespace Palet\Framework\Foundation;

use RuntimeException;

/**
 * Başlatma (Bootstrap) aşamasından önce ortamı doğrular.
 */
final class EnvironmentValidator
{
    /**
     * Zorunlu PHP eklentileri listesi.
     */
    private const REQUIRED_EXTENSIONS = [
        'mbstring',
        'json',
        'pdo',
    ];

    /**
     * Ortamın Framework için uygunluğunu denetler.
     *
     * @throws RuntimeException
     */
    public static function validate(): void
    {
        self::checkPhpVersion();
        self::checkExtensions();
    }

    /**
     * @throws RuntimeException
     */
    private static function checkPhpVersion(): void
    {
        $minVersion = Version::getMinimumPhpVersion();

        if (version_compare(PHP_VERSION, $minVersion, '<')) {
            throw new RuntimeException(
                sprintf('Palet Framework requires PHP version %s or higher. You are running %s.', $minVersion, PHP_VERSION)
            );
        }
    }

    /**
     * @throws RuntimeException
     */
    private static function checkExtensions(): void
    {
        foreach (self::REQUIRED_EXTENSIONS as $ext) {
            if (!extension_loaded($ext)) {
                throw new RuntimeException(
                    sprintf('The required PHP extension "%s" is not loaded.', $ext)
                );
            }
        }
    }
}
