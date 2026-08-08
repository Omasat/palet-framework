<?php

declare(strict_types=1);

namespace Palet\Framework\Validation;

use Exception;
use Palet\Framework\Contracts\Validation\MessageBagInterface;

class ValidationException extends Exception
{
    protected MessageBagInterface $errors;

    public function __construct(MessageBagInterface $errors, string $message = "The given data was invalid.")
    {
        parent::__construct($message);
        $this->errors = $errors;
    }

    public function errors(): MessageBagInterface
    {
        return $this->errors;
    }
}
