<?php

declare(strict_types=1);

namespace Palet\Framework\Foundation\Exceptions;

class SecurityMasker
{
    /**
     * @var array<int, string>
     */
    protected array $sensitiveKeys = [
        'password',
        'secret',
        'key',
        'token',
        'auth',
        'credentials',
        'APP_KEY',
        'DB_PASSWORD'
    ];

    /**
     * Mask sensitive values in an array.
     */
    public function mask(array $data): array
    {
        $masked = [];

        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $masked[$key] = $this->mask($value);
            } else {
                $masked[$key] = $this->isSensitive((string) $key) ? '[MASKED]' : $value;
            }
        }

        return $masked;
    }

    /**
     * Determine if a given key is considered sensitive.
     */
    protected function isSensitive(string $key): bool
    {
        $lowercaseKey = strtolower($key);

        foreach ($this->sensitiveKeys as $sensitive) {
            if (str_contains($lowercaseKey, strtolower($sensitive))) {
                return true;
            }
        }

        return false;
    }
}
