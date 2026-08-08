<?php

declare(strict_types=1);

namespace Palet\Framework\Http\Kernel;

use Palet\Framework\Contracts\Foundation\ApplicationInterface;
use Palet\Framework\Contracts\Http\Message\RequestInterface;
use Palet\Framework\Contracts\Http\Message\ResponseInterface;
use Palet\Framework\Http\Message\Response;
use Palet\Framework\Pipeline\Pipeline;
use Throwable;

class RequestDispatcher
{
    protected ApplicationInterface $app;
    
    /**
     * @var array<int, class-string>
     */
    protected array $middleware = [];

    public function __construct(ApplicationInterface $app)
    {
        $this->app = $app;
    }

    public function dispatch(RequestInterface $request): ResponseInterface
    {
        try {
            $pipeline = new Pipeline($this->app);
            
            return $pipeline->send($request)
                ->through($this->middleware)
                ->then($this->dispatchToRouter());
        } catch (Throwable $e) {
            return $this->handleException($request, $e);
        }
    }
    
    protected function dispatchToRouter(): \Closure
    {
        return function (RequestInterface $request) {
            if (!$this->app->has(\Palet\Framework\Contracts\Routing\RouterInterface::class)) {
                return new Response(404, [], 'Not Found (Router Stub)');
            }

            $router = $this->app->make(\Palet\Framework\Contracts\Routing\RouterInterface::class);

            if (!$router instanceof \Palet\Framework\Contracts\Routing\RouterInterface) {
                return new Response(404, [], 'Not Found (Router Stub)');
            }

            return $router->dispatch($request);
        };
    }
    
    protected function handleException(RequestInterface $request, Throwable $e): ResponseInterface
    {
        // @todo In future sprints, this will delegate to ExceptionHandler
        return new Response(500, [], 'Internal Server Error: ' . $e->getMessage());
    }
}
