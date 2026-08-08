<?php

declare(strict_types=1);

namespace Tests\Foundation\Providers;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Foundation\Application;
use Palet\Framework\Foundation\Providers\DeferredProviderRepository;
use Palet\Framework\Foundation\Providers\ServiceProvider;
use Palet\Framework\Contracts\Support\DeferrableProviderInterface;

class LazyDummyProvider extends ServiceProvider implements DeferrableProviderInterface
{
    public function provides(): array
    {
        return ['mailer', 'view'];
    }
}

class DeferredProviderRepositoryTest extends TestCase
{
    public function test_loads_provider_when_service_is_requested()
    {
        $app = new Application(__DIR__);
        $deferredRepo = new DeferredProviderRepository($app);

        // Map abstract service names to provider class names
        $deferredRepo->setDeferredServices([
            'mailer' => LazyDummyProvider::class,
            'view' => LazyDummyProvider::class,
        ]);

        $this->assertNull($app->getProvider(LazyDummyProvider::class));

        $deferredRepo->load('mailer');

        $this->assertInstanceOf(LazyDummyProvider::class, $app->getProvider(LazyDummyProvider::class));
    }
}
