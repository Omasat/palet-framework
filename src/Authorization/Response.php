<?php

declare(strict_types=1);

namespace Palet\Framework\Authorization;

class Response
{
    protected bool $allowed;
    protected string $message;
    protected mixed $code;

    public function __construct(bool $allowed, string $message = '', mixed $code = null)
    {
        $this->allowed = $allowed;
        $this->message = $message;
        $this->code = $code;
    }

    public static function allow(string $message = '', mixed $code = null): static
    {
        return new static(true, $message, $code);
    }

    public static function deny(string $message = '', mixed $code = null): static
    {
        return new static(false, $message, $code);
    }

    public function allowed(): bool
    {
        return $this->allowed;
    }

    public function denied(): bool
    {
        return !$this->allowed;
    }

    public function message(): string
    {
        return $this->message;
    }

    public function code(): mixed
    {
        return $this->code;
    }

    public function authorize(): static
    {
        if ($this->denied()) {
            throw new AuthorizationException($this->message(), $this->code());
        }

        return $this;
    }
}
