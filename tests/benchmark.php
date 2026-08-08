<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use Palet\Framework\Foundation\Application;
use Palet\Framework\Http\Message\Request;
use Palet\Framework\Http\Message\Uri;

function measure(string $name, callable $callback) {
    $startMemory = memory_get_usage();
    $startTime = microtime(true);
    
    $callback();
    
    $endTime = microtime(true);
    $endMemory = memory_get_usage();
    
    $timeMs = ($endTime - $startTime) * 1000;
    $memoryKb = ($endMemory - $startMemory) / 1024;
    
    printf("%-30s | %8.2f ms | %8.2f KB\n", $name, $timeMs, $memoryKb);
}

echo "Palet Framework Benchmark\n";
echo str_repeat('-', 60) . "\n";
printf("%-30s | %11s | %11s\n", 'Metric', 'Time', 'Memory');
echo str_repeat('-', 60) . "\n";

$app = null;

measure('Framework Boot Time', function () use (&$app) {
    $app = new Application(__DIR__);
    $app->boot();
});

// Create Router and 100 routes
$router = null;
measure('Router Init & 100 Routes', function () use ($app, &$router) {
    $app->bind(\Palet\Framework\Contracts\Routing\Dispatcher\ControllerDispatcherInterface::class, \Palet\Framework\Routing\Dispatcher\ControllerDispatcher::class);
    $app->bind(\Palet\Framework\Contracts\Routing\Dispatcher\ActionInvokerInterface::class, \Palet\Framework\Routing\Dispatcher\ActionInvoker::class);
    $app->bind(\Palet\Framework\Contracts\Support\Invocation\MethodInvokerInterface::class, \Palet\Framework\Support\Invocation\MethodInvoker::class);
    $app->bind(\Palet\Framework\Contracts\Support\Invocation\ParameterResolverInterface::class, \Palet\Framework\Support\Invocation\ParameterResolver::class);
    
    $router = new \Palet\Framework\Routing\Router(null, $app);
    for ($i = 0; $i < 100; $i++) {
        $router->get('/route/' . $i, function () { return 'OK'; });
    }
});

measure('Route Matching (100x Static)', function () use ($router) {
    $request = new Request('GET', new Uri('http://localhost/route/99'));
    $reflection = new \ReflectionClass($router);
    $method = $reflection->getMethod('findRoute');
    $method->setAccessible(true);
    for ($i = 0; $i < 100; $i++) {
        $method->invoke($router, $request);
    }
});

measure('Route Matching (100x Dynamic)', function () use ($router) {
    // Add a dynamic route at the end
    $router->get('/user/{id}', function () { return 'OK'; });
    
    $request = new Request('GET', new Uri('http://localhost/user/123'));
    $reflection = new \ReflectionClass($router);
    $method = $reflection->getMethod('findRoute');
    $method->setAccessible(true);
    for ($i = 0; $i < 100; $i++) {
        $method->invoke($router, $request);
    }
});

measure('Route Matching (1000x Dynamic)', function () use ($router) {
    $request = new Request('GET', new Uri('http://localhost/user/123'));
    $reflection = new \ReflectionClass($router);
    $method = $reflection->getMethod('findRoute');
    $method->setAccessible(true);
    for ($i = 0; $i < 1000; $i++) {
        $method->invoke($router, $request);
    }
});

measure('Route Matching (5000x Dynamic)', function () use ($router) {
    $request = new Request('GET', new Uri('http://localhost/user/123'));
    $reflection = new \ReflectionClass($router);
    $method = $reflection->getMethod('findRoute');
    $method->setAccessible(true);
    for ($i = 0; $i < 5000; $i++) {
        $method->invoke($router, $request);
    }
});

measure('Container Resolve (100x)', function () use ($app) {
    $app->bind('test_service', function () {
        return new \stdClass();
    });
    for ($i = 0; $i < 100; $i++) {
        $app->make('test_service');
    }
});

measure('Container Resolve Singleton (100x)', function () use ($app) {
    $app->singleton('test_singleton', function () {
        return new \stdClass();
    });
    for ($i = 0; $i < 100; $i++) {
        $app->make('test_singleton');
    }
});

echo str_repeat('-', 60) . "\n";
printf("Peak Memory: %.2f MB\n", memory_get_peak_usage() / 1024 / 1024);
