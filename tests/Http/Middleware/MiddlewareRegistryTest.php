<?php

declare(strict_types=1);

namespace Tests\Http\Middleware;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Http\Middleware\MiddlewareRegistry;
use Palet\Framework\Http\Middleware\MiddlewareResolver;
use InvalidArgumentException;

class MiddlewareRegistryTest extends TestCase
{
    public function test_registers_and_resolves_global_middleware()
    {
        $registry = new MiddlewareRegistry();
        $registry->pushGlobal('App\Http\Middleware\TrimStrings');
        
        $this->assertEquals(['App\Http\Middleware\TrimStrings'], $registry->getGlobalMiddleware());
    }

    public function test_registers_and_resolves_aliases()
    {
        $registry = new MiddlewareRegistry();
        $registry->alias('auth', 'App\Http\Middleware\Authenticate');
        
        $resolver = new MiddlewareResolver($registry);
        
        $this->assertEquals('App\Http\Middleware\Authenticate', $resolver->resolve('auth'));
        $this->assertEquals('App\Http\Middleware\Unregistered', $resolver->resolve('App\Http\Middleware\Unregistered'));
    }

    public function test_registers_and_resolves_groups()
    {
        $registry = new MiddlewareRegistry();
        $registry->alias('auth', 'App\Http\Middleware\Authenticate');
        $registry->group('web', [
            'App\Http\Middleware\StartSession',
            'auth'
        ]);
        
        $resolver = new MiddlewareResolver($registry);
        
        $resolved = $resolver->resolveGroup('web');
        $this->assertEquals([
            'App\Http\Middleware\StartSession',
            'App\Http\Middleware\Authenticate'
        ], $resolved);
    }

    public function test_throws_exception_for_invalid_group()
    {
        $registry = new MiddlewareRegistry();
        $resolver = new MiddlewareResolver($registry);
        
        $this->expectException(InvalidArgumentException::class);
        $resolver->resolveGroup('invalid');
    }
}
