<?php

declare(strict_types=1);

namespace Tests\Foundation\Providers;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Foundation\Application;
use Palet\Framework\Foundation\Providers\ProviderRepository;
use Palet\Framework\Foundation\Providers\ServiceProvider;

class RepoDummyProvider1 extends ServiceProvider {}
class RepoDummyProvider2 extends ServiceProvider {}

class ProviderRepositoryTest extends TestCase
{
    public function test_loads_array_of_providers()
    {
        $app = new Application(__DIR__);
        $repository = new ProviderRepository($app);

        $repository->load([
            RepoDummyProvider1::class,
            RepoDummyProvider2::class,
        ]);

        $this->assertInstanceOf(RepoDummyProvider1::class, $app->getProvider(RepoDummyProvider1::class));
        $this->assertInstanceOf(RepoDummyProvider2::class, $app->getProvider(RepoDummyProvider2::class));
    }
}
