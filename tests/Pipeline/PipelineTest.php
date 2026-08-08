<?php

declare(strict_types=1);

namespace Tests\Pipeline;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Pipeline\Pipeline;

class TrimStrings
{
    public function handle($passable, $next)
    {
        $passable = trim($passable);
        return $next($passable);
    }
}

class UpperCaseStrings
{
    public function handle($passable, $next)
    {
        $passable = strtoupper($passable);
        return $next($passable);
    }
}

class PipelineTest extends TestCase
{
    public function test_pipeline_executes_in_order()
    {
        $pipeline = new Pipeline();

        $result = $pipeline->send('  hello world  ')
            ->through([
                TrimStrings::class,
                UpperCaseStrings::class,
                function ($passable, $next) {
                    return $next($passable . '!');
                }
            ])
            ->then(function ($passable) {
                return $passable;
            });

        $this->assertEquals('HELLO WORLD!', $result);
    }

    public function test_pipeline_return_method()
    {
        $pipeline = new Pipeline();

        $result = $pipeline->send('test')
            ->through([
                function ($passable, $next) {
                    return $next($passable . ' passed');
                }
            ])
            ->thenReturn();

        $this->assertEquals('test passed', $result);
    }
}
