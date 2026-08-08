<?php

declare(strict_types=1);

namespace Palet\Framework\Routing\Exceptions;

use RuntimeException;

class MethodNotAllowedException extends RuntimeException
{
    protected array $allowedMethods;

    public function __construct(array $allowedMethods, string $message = 'Method not allowed.')
    {
        parent::__construct($message, 405);
        $this->allowedMethods = $allowedMethods;
    }

    public function getAllowedMethods(): array
    {
        return $this->allowedMethods;
    }
}
