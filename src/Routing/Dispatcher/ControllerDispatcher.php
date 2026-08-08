<?php

declare(strict_types=1);

namespace Palet\Framework\Routing\Dispatcher;

use Palet\Framework\Contracts\Http\Message\RequestInterface;
use Palet\Framework\Contracts\Http\Message\ResponseInterface;
use Palet\Framework\Contracts\Routing\Dispatcher\ControllerDispatcherInterface;
use Palet\Framework\Contracts\Support\Invocation\MethodInvokerInterface;
use Palet\Framework\Routing\Matching\RouteMatch;

class ControllerDispatcher implements ControllerDispatcherInterface
{
    protected ActionResolver $actionResolver;
    protected ControllerResolver $controllerResolver;
    protected MethodInvokerInterface $methodInvoker;
    protected ActionResultNormalizer $resultNormalizer;

    public function __construct(
        ActionResolver $actionResolver,
        ControllerResolver $controllerResolver,
        MethodInvokerInterface $methodInvoker,
        ActionResultNormalizer $resultNormalizer
    ) {
        $this->actionResolver = $actionResolver;
        $this->controllerResolver = $controllerResolver;
        $this->methodInvoker = $methodInvoker;
        $this->resultNormalizer = $resultNormalizer;
    }

    public function dispatch(RequestInterface $request, RouteMatch $match): ResponseInterface
    {
        $action = $match->route->getAction();
        $parameters = $match->parameters;
        
        $metadata = $this->actionResolver->resolve($action);
        
        $callableAction = $this->getCallableAction($metadata);
        
        $context = clone $request; // Actually we should pass a context object. Let's create an InvocationContext.
        $invocationContext = new \Palet\Framework\Support\Invocation\InvocationContext(array_merge($parameters, [
            RequestInterface::class => $request,
            get_class($request) => $request,
            'request' => $request
        ]));
        
        $result = $this->methodInvoker->invoke($callableAction, $invocationContext);
        
        return $this->resultNormalizer->normalize($result);
    }

    protected function getCallableAction(ActionMetadata $metadata): callable
    {
        if ($metadata->isClosure) {
            return $metadata->closure;
        }

        $controller = $this->controllerResolver->resolve($metadata->controllerClass);

        return [$controller, $metadata->method];
    }
}
