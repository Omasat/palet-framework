<?php

declare(strict_types=1);

namespace Tests\Http\Middleware;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Http\Middleware\MiddlewarePriorityResolver;

class MiddlewarePriorityResolverTest extends TestCase
{
    public function test_sorts_middleware_by_priority()
    {
        $resolver = new MiddlewarePriorityResolver();
        $resolver->setPriorityMap([
            'App\Middleware\First',
            'App\Middleware\Second',
            'App\Middleware\Third',
        ]);
        
        $unsorted = [
            'App\Middleware\Third',
            'App\Middleware\Unknown',
            'App\Middleware\First',
        ];
        
        $sorted = $resolver->sort($unsorted);
        
        $this->assertEquals([
            'App\Middleware\First',
            'App\Middleware\Third',
            'App\Middleware\Unknown',
        ], $sorted);
    }
}
