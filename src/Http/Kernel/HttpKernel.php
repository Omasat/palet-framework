<?php

declare(strict_types=1);

namespace Palet\Framework\Http\Kernel;

use Palet\Framework\Contracts\Foundation\ApplicationInterface;
use Palet\Framework\Contracts\Http\Kernel\HttpKernelInterface;
use Palet\Framework\Contracts\Http\Kernel\RequestLifecycleInterface;
use Palet\Framework\Contracts\Http\Kernel\TerminableKernelInterface;
use Palet\Framework\Contracts\Http\Message\RequestInterface;
use Palet\Framework\Contracts\Http\Message\ResponseInterface;
use Palet\Framework\Foundation\Kernel\Kernel;
use Throwable;

class HttpKernel extends Kernel implements HttpKernelInterface, TerminableKernelInterface
{
    protected RequestDispatcher $requestDispatcher;
    protected ResponseDispatcher $responseDispatcher;

    public function __construct(ApplicationInterface $app)
    {
        parent::__construct($app);
        
        $this->requestDispatcher = new RequestDispatcher($this->app);
        $this->responseDispatcher = new ResponseDispatcher();
    }

    public function handle(RequestInterface $request): ResponseInterface
    {
        try {
            $this->bootstrap();
            
            $context = RequestContext::fromRequest($request);
            $this->app->instance(RequestContext::class, $context);
            
            $this->triggerLifecycle('onStart', $request);
            
            $response = $this->requestDispatcher->dispatch($request);
            
            $this->triggerLifecycle('onSend', $request, $response);
            
            return $response;
        } catch (Throwable $e) {
            return $this->handleException($request, $e);
        }
    }

    public function sendRequest(RequestInterface $request): void
    {
        $response = $this->handle($request);
        $this->responseDispatcher->send($response);
        $this->terminateRequest($request, $response);
    }

    public function terminateRequest(RequestInterface $request, ResponseInterface $response): void
    {
        parent::terminate();
    }

    protected function triggerLifecycle(string $method, mixed ...$args): void
    {
        if ($this->app->has(RequestLifecycleInterface::class)) {
            $lifecycle = $this->app->make(RequestLifecycleInterface::class);
            $lifecycle->$method(...$args);
        }
    }
    
    protected function handleException(RequestInterface $request, Throwable $e): ResponseInterface
    {
        // Fallback for fatal errors during bootstrap/lifecycle triggers
        // The RequestDispatcher has its own internal handler, but this is the ultimate fallback
        return new \Palet\Framework\Http\Message\Response(500, [], 'Fatal Error: ' . $e->getMessage());
    }
}
