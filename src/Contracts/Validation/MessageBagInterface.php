<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Validation;

interface MessageBagInterface
{
    /**
     * Determine if messages exist for a given key.
     */
    public function has(string $key): bool;

    /**
     * Get the first message for a given key.
     */
    public function first(string $key): ?string;

    /**
     * Get all of the messages for a given key.
     */
    public function get(string $key): array;

    /**
     * Get all of the messages.
     */
    public function all(): array;

    /**
     * Add a message to the bag.
     */
    public function add(string $key, string $message): static;

    /**
     * Convert the message bag to its array representation.
     */
    public function toArray(): array;
}
