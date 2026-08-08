<?php

declare(strict_types=1);

namespace Tests\Foundation;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Foundation\Application;
use Palet\Framework\Foundation\Bootstrap\BootstrapManager;
use Palet\Framework\Foundation\Bootstrap\BootstrapperInterface;
use Palet\Framework\Contracts\Foundation\ApplicationInterface;

class DummyBootstrapper implements BootstrapperInterface
{
    public static bool $executed = false;

    public function bootstrap(ApplicationInterface $app): void
    {
        self::$executed = true;
    }
}

class BootstrapManagerTest extends TestCase
{
    public function test_manager_runs_bootstrappers()
    {
        $app = new Application(__DIR__);
        $manager = new BootstrapManager($app);

        DummyBootstrapper::$executed = false;

        $manager->bootstrapWith([DummyBootstrapper::class]);

        $this->assertTrue(DummyBootstrapper::$executed);
    }
}
