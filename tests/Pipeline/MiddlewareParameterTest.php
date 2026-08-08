<?php

declare(strict_types=1);

namespace Tests\Pipeline;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Pipeline\Pipeline;

class RoleMiddleware
{
    public function handle($passable, $next, $role)
    {
        $passable['required_role'] = $role;
        return $next($passable);
    }
}

class MultipleParamsMiddleware
{
    public function handle($passable, $next, $param1, $param2)
    {
        $passable['params'] = [$param1, $param2];
        return $next($passable);
    }
}

class MiddlewareParameterTest extends TestCase
{
    public function test_passes_parameters_to_pipe()
    {
        $pipeline = new Pipeline();

        $result = $pipeline->send([])
            ->through([
                RoleMiddleware::class . ':admin',
            ])
            ->thenReturn();

        $this->assertEquals('admin', $result['required_role']);
    }

    public function test_passes_multiple_parameters_to_pipe()
    {
        $pipeline = new Pipeline();

        $result = $pipeline->send([])
            ->through([
                MultipleParamsMiddleware::class . ':foo,bar',
            ])
            ->thenReturn();

        $this->assertEquals(['foo', 'bar'], $result['params']);
    }
}
