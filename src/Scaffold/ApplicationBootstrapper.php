<?php

declare(strict_types=1);

namespace Palet\Framework\Scaffold;

class ApplicationBootstrapper
{
    public function generate(string $targetPath): void
    {
        $bootstrapDir = $targetPath . DIRECTORY_SEPARATOR . 'bootstrap';
        if (!is_dir($bootstrapDir)) {
            mkdir($bootstrapDir, 0755, true);
        }

        $appPhp = <<<PHP
<?php

use Palet\Framework\Foundation\Application;

\$app = new Application(
    \$_ENV['APP_BASE_PATH'] ?? dirname(__DIR__)
);

// Register Core Bindings
\$app->singleton(
    Palet\Framework\Contracts\Http\KernelInterface::class,
    App\Http\Kernel::class
);

\$app->singleton(
    Palet\Framework\Contracts\Console\KernelInterface::class,
    App\Console\Kernel::class
);

\$app->singleton(
    Palet\Framework\Contracts\Debug\ExceptionHandlerInterface::class,
    App\Exceptions\Handler::class
);

return \$app;
PHP;

        file_put_contents($bootstrapDir . DIRECTORY_SEPARATOR . 'app.php', $appPhp);
    }
}
