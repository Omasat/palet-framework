<?php

declare(strict_types=1);

namespace Palet\Framework\Auth;

use Palet\Framework\Contracts\Auth\PasswordHasherInterface;

class PasswordHasher implements PasswordHasherInterface
{
    public function make(string $value, array $options = []): string
    {
        return password_hash($value, PASSWORD_DEFAULT, $options);
    }

    public function check(string $value, string $hashedValue, array $options = []): bool
    {
        if (strlen($hashedValue) === 0) {
            return false;
        }

        return password_verify($value, $hashedValue);
    }

    public function needsRehash(string $hashedValue, array $options = []): bool
    {
        return password_needs_rehash($hashedValue, PASSWORD_DEFAULT, $options);
    }
}
