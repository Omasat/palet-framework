<?php

declare(strict_types=1);

namespace Tests\Support\Invocation;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Support\Invocation\ReflectionMetadataCache;

class ReflectionMetadataCacheTest extends TestCase
{
    protected function tearDown(): void
    {
        ReflectionMetadataCache::clear();
        parent::tearDown();
    }

    public function test_caches_reflection_parameters()
    {
        $cache = new ReflectionMetadataCache();
        
        $action = function (int $id) {};
        
        $params1 = $cache->getParameters($action);
        $params2 = $cache->getParameters($action);
        
        $this->assertSame($params1, $params2);
        $this->assertCount(1, $params1);
        $this->assertEquals('id', $params1[0]->getName());
    }

    public function test_checks_if_method_is_public()
    {
        $cache = new ReflectionMetadataCache();
        
        $controller = new DummyController();
        
        $this->assertTrue($cache->isPublic([$controller, 'index']));
        $this->assertFalse($cache->isPublic([$controller, 'secret']));
    }
}

class DummyController
{
    public function index() {}
    private function secret() {}
}
