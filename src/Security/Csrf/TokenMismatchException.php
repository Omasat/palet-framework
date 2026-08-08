<?php

declare(strict_types=1);

namespace Palet\Framework\Security\Csrf;

use Exception;

class TokenMismatchException extends Exception
{
    protected mixed $status = 419;

    public function __construct(string $message = 'CSRF token mismatch.', int $code = 0, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public function status(): mixed
    {
        return $this->status;
    }
}
