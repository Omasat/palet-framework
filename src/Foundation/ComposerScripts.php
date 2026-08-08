<?php

declare(strict_types=1);

namespace Palet\Framework\Foundation;

class ComposerScripts
{
    /**
     * Composer post-autoload-dump hook.
     *
     * This stub ensures the project can install cleanly even when
     * the custom Palet framework package defines this script.
     */
    public static function postAutoloadDump(): void
    {
        $bootstrapCache = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'bootstrap' . DIRECTORY_SEPARATOR . 'cache';

        if (!is_dir($bootstrapCache)) {
            mkdir($bootstrapCache, 0755, true);
        }
    }
}
