<?php

declare(strict_types=1);

namespace Palet\Framework\Foundation\Bootstrap;

use Palet\Framework\Contracts\Foundation\ApplicationInterface;

/**
 * Belirlenen Bootstrapper sınıflarını sırasıyla çalıştırarak framework'ü ayağa kaldırır.
 */
final class BootstrapManager
{
    private ApplicationInterface $app;

    public function __construct(ApplicationInterface $app)
    {
        $this->app = $app;
    }

    /**
     * @param class-string<BootstrapperInterface>[] $bootstrappers
     */
    public function bootstrapWith(array $bootstrappers): void
    {
        foreach ($bootstrappers as $bootstrapper) {
            $this->app->make($bootstrapper)->bootstrap($this->app);
        }
    }
}
