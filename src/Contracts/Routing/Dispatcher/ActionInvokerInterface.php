<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Routing\Dispatcher;

use Palet\Framework\Contracts\Http\Message\RequestInterface;

interface ActionInvokerInterface
{
    /**
     * Invoke the given callable action with the route parameters.
     */
    public function invoke(mixed $action, RequestInterface $request, array $parameters): mixed;
}
