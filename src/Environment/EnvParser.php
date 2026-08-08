<?php

declare(strict_types=1);

namespace Palet\Framework\Environment;

/**
 * .env dosyası içeriğini okuyup satır satır ayrıştırır ve
 * değerleri doğru tiplere (Type Casting) çevirir.
 */
class EnvParser
{
    /**
     * Parse the given .env file contents.
     *
     * @param string $content
     * @return array<string, mixed>
     */
    public function parse(string $content): array
    {
        $lines = explode("\n", str_replace(["\r\n", "\n\r", "\r"], "\n", $content));
        $parsed = [];

        foreach ($lines as $line) {
            $line = trim($line);

            // Boş satırları veya yorum satırlarını atla
            if ($line === '' || str_starts_with($line, '#')) {
                continue;
            }

            if (str_contains($line, '=')) {
                [$name, $value] = explode('=', $line, 2);
                $name = trim($name);
                $value = trim($value);

                if (!empty($name)) {
                    $parsed[$name] = $this->castValue($value);
                }
            }
        }

        return $parsed;
    }

    /**
     * Cast the environment variable value to the appropriate type.
     */
    protected function castValue(string $value): mixed
    {
        // Tırnak işaretlerini (quotes) temizle
        if (preg_match('/^"(.*)"$/', $value, $matches) || preg_match("/^'(.*)'$/", $value, $matches)) {
            return $matches[1];
        }

        $lowercaseValue = strtolower($value);

        return match ($lowercaseValue) {
            'true', '(true)' => true,
            'false', '(false)' => false,
            'empty', '(empty)' => '',
            'null', '(null)' => null,
            default => $this->castNumeric($value),
        };
    }

    /**
     * Cast to numeric if possible, otherwise return string.
     */
    protected function castNumeric(string $value): mixed
    {
        if (is_numeric($value)) {
            return str_contains($value, '.') ? (float) $value : (int) $value;
        }

        return $value;
    }
}
