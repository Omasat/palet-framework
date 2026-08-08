<?php

declare(strict_types=1);

namespace Palet\Framework\Routing\Dispatcher;

use Closure;
use InvalidArgumentException;

class ActionResolver
{
    /**
     * Resolve the route action into metadata.
     */
    public function resolve(mixed $action): ActionMetadata
    {
        if ($action instanceof Closure) {
            return new ActionMetadata(true, null, null, $action);
        }

        if (is_string($action)) {
            if (str_contains($action, '@')) {
                [$class, $method] = explode('@', $action);
                return new ActionMetadata(false, $class, $method);
            }

            return new ActionMetadata(false, $action, '__invoke');
        }

        if (is_array($action) && count($action) === 2) {
            return new ActionMetadata(false, $action[0], $action[1]);
        }
        
        if (is_callable($action) && is_array($action)) {
            return new ActionMetadata(false, get_class($action[0]), $action[1]);
        }

        throw new InvalidArgumentException('Invalid route action provided.');
    }
}
