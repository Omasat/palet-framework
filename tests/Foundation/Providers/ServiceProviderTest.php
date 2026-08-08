<?php

declare(strict_types=1);

namespace Tests\Foundation\Providers;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Foundation\Application;
use Palet\Framework\Foundation\Providers\ServiceProvider;

class DummyServiceProvider extends ServiceProvider
{
    public bool $registered = false;
    public bool $booted = false;

    public function register(): void
    {
        $this->registered = true;
    }

    public function boot(): void
    {
        $this->booted = true;
    }
}

class ServiceProviderTest extends TestCase
{
    public function test_provider_can_be_registered_and_booted()
    {
        $app = new Application(__DIR__);
        $provider = new DummyServiceProvider($app);

        $app->register($provider);

        $this->assertTrue($provider->registered);
        
        // Boot edilmediği için false olmalı
        $this->assertFalse($provider->booted);

        $app->boot();

        // Boot edildiği için true olmalı
        $this->assertTrue($provider->booted);
    }
}
