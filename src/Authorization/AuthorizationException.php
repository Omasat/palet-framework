<?php

declare(strict_types=1);

namespace Palet\Framework\Authorization;

use Exception;

class AuthorizationException extends Exception
{
    protected mixed $status = 403;

    public function __construct(string $message = 'This action is unauthorized.', mixed $code = 0, \Throwable $previous = null)
    {
        if (is_int($code)) {
            parent::__construct($message, $code, $previous);
        } else {
            parent::__construct($message, 0, $previous);
        }
    }

    public function status(): mixed
    {
        return $this->status;
    }
}
