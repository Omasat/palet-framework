<?php

declare(strict_types=1);

namespace Tests\Routing;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Routing\RouteGroup;

class RouteGroupTest extends TestCase
{
    public function test_merge_prefixes()
    {
        $old = ['prefix' => 'api'];
        $new = ['prefix' => 'v1'];
        
        $merged = RouteGroup::merge($new, $old);
        
        $this->assertEquals('api/v1', $merged['prefix']);
    }

    public function test_merge_names()
    {
        $old = ['name' => 'admin.'];
        $new = ['name' => 'users'];
        
        $merged = RouteGroup::merge($new, $old);
        
        $this->assertEquals('admin.users', $merged['name']);
    }

    public function test_merge_middleware()
    {
        $old = ['middleware' => 'web'];
        $new = ['middleware' => ['auth', 'admin']];
        
        $merged = RouteGroup::merge($new, $old);
        
        $this->assertEquals(['web', 'auth', 'admin'], $merged['middleware']);
    }
}
