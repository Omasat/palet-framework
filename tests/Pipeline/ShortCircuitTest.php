<?php

declare(strict_types=1);

namespace Tests\Pipeline;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Pipeline\Pipeline;

class CheckAuth
{
    public function handle($passable, $next)
    {
        if ($passable['role'] !== 'admin') {
            return 'Unauthorized'; // Short circuit, next is not called
        }
        
        return $next($passable);
    }
}

class ShortCircuitTest extends TestCase
{
    public function test_pipeline_can_short_circuit()
    {
        $pipeline = new Pipeline();

        $result = $pipeline->send(['role' => 'guest'])
            ->through([
                CheckAuth::class,
                function ($passable, $next) {
                    $passable['authorized'] = true;
                    return $next($passable);
                }
            ])
            ->thenReturn();

        $this->assertEquals('Unauthorized', $result);
    }

    public function test_pipeline_continues_if_no_short_circuit()
    {
        $pipeline = new Pipeline();

        $result = $pipeline->send(['role' => 'admin'])
            ->through([
                CheckAuth::class,
                function ($passable, $next) {
                    $passable['authorized'] = true;
                    return $next($passable);
                }
            ])
            ->thenReturn();

        $this->assertTrue($result['authorized']);
    }
}
