<?php

declare(strict_types=1);

namespace Palet\Framework\Environment;

use Palet\Framework\Contracts\Config\EnvironmentInterface;

class EnvironmentDetector
{
    /**
     * Ortamın adını saptar (production, local, testing vb.).
     */
    public static function detect(EnvironmentInterface $env, string $default = 'production'): string
    {
        $environment = $env->get('APP_ENV', $default);

        if (is_string($environment)) {
            return strtolower($environment);
        }

        return $default;
    }
}
