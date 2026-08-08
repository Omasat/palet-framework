<?php

declare(strict_types=1);

namespace Palet\Framework\Routing\Exceptions;

use RuntimeException;

class RouteNotFoundException extends RuntimeException
{
    public function __construct(string $message = 'Route not found.')
    {
        parent::__construct($message, 404);
    }
}
