<?php

declare(strict_types=1);

namespace Tests\Support\Invocation;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Support\Invocation\MethodInvoker;
use Palet\Framework\Support\Invocation\ReflectionMetadataCache;
use Palet\Framework\Support\Invocation\ArgumentMapper;
use Palet\Framework\Support\Invocation\DependencyResolver;
use Palet\Framework\Support\Invocation\ParameterResolver;
use Palet\Framework\Support\Invocation\InvocationContext;
use Palet\Framework\Contracts\Foundation\ApplicationInterface;

class MethodInvokerTest extends TestCase
{
    public function test_invokes_closure_with_injected_dependencies()
    {
        $app = $this->createMock(ApplicationInterface::class);
        $app->method('has')->willReturnMap([
            [\stdClass::class, true]
        ]);
        $app->method('make')->willReturnMap([
            [\stdClass::class, new \stdClass()]
        ]);

        $cache = new ReflectionMetadataCache();
        $depResolver = new DependencyResolver($app);
        $paramResolver = new ParameterResolver($depResolver);
        $mapper = new ArgumentMapper($paramResolver);
        $invoker = new MethodInvoker($cache, $mapper);

        $action = function (\stdClass $dep, int $id, string $name = 'guest') {
            return $id . '-' . $name . '-' . get_class($dep);
        };

        $context = new InvocationContext(['id' => 123]);

        $result = $invoker->invoke($action, $context);

        $this->assertEquals('123-guest-stdClass', $result);
    }
}
