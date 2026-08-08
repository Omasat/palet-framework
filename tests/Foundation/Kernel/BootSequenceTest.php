<?php

declare(strict_types=1);

namespace Tests\Foundation\Kernel;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Foundation\Kernel\BootSequence;
use Palet\Framework\Contracts\Foundation\ApplicationInterface;
use Palet\Framework\Contracts\Foundation\Kernel\BootableInterface;

class BootstrapperOne implements BootableInterface
{
    public static bool $booted = false;

    public function bootstrap(\Palet\Framework\Contracts\Foundation\ApplicationInterface $app, \Closure $next): mixed
    {
        self::$booted = true;
        return $next($app);
    }
}

class BootstrapperTwo implements BootableInterface
{
    public static bool $booted = false;

    public function bootstrap(\Palet\Framework\Contracts\Foundation\ApplicationInterface $app, \Closure $next): mixed
    {
        self::$booted = true;
        return $next($app);
    }
}

class BootSequenceTest extends TestCase
{
    protected function tearDown(): void
    {
        BootstrapperOne::$booted = false;
        BootstrapperTwo::$booted = false;
    }

    public function test_runs_bootstrappers_in_order()
    {
        $app = $this->createMock(ApplicationInterface::class);
        $app->method('make')->willReturnCallback(function($abstract) {
            return new $abstract();
        });
        
        $sequence = new BootSequence($app, [
            BootstrapperOne::class,
            BootstrapperTwo::class,
        ]);
        
        $sequence->run();
        
        $this->assertTrue(BootstrapperOne::$booted);
        $this->assertTrue(BootstrapperTwo::$booted);
    }
}
