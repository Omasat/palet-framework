<?php

declare(strict_types=1);

namespace Palet\Framework\Foundation\Bootstrap;

/**
 * Framework başlatılırken sırasıyla çalışacak sınıfların listesini tutar.
 */
final class BootstrapSequence
{
    /**
     * @var class-string<BootstrapperInterface>[]
     */
    private const SEQUENCE = [
        LoadEnvironmentVariables::class,
        LoadConfiguration::class,
        InitializeContainer::class,
        RegisterCoreServices::class,
        RegisterServiceProviders::class,
        BootServiceProviders::class,
        RegisterExceptionHandler::class,
        RegisterLogger::class,
    ];

    /**
     * Get the default bootstrap sequence.
     *
     * @return class-string<BootstrapperInterface>[]
     */
    public static function get(): array
    {
        return self::SEQUENCE;
    }
}
