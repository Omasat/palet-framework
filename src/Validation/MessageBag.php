<?php

declare(strict_types=1);

namespace Palet\Framework\Validation;

use Palet\Framework\Contracts\Validation\MessageBagInterface;

class MessageBag implements MessageBagInterface
{
    protected array $messages = [];

    public function __construct(array $messages = [])
    {
        foreach ($messages as $key => $message) {
            $this->add($key, $message);
        }
    }

    public function has(string $key): bool
    {
        return isset($this->messages[$key]) && count($this->messages[$key]) > 0;
    }

    public function first(string $key): ?string
    {
        return $this->messages[$key][0] ?? null;
    }

    public function get(string $key): array
    {
        return $this->messages[$key] ?? [];
    }

    public function all(): array
    {
        return $this->messages;
    }

    public function add(string $key, string $message): static
    {
        $this->messages[$key][] = $message;
        return $this;
    }

    public function toArray(): array
    {
        return $this->messages;
    }
}
