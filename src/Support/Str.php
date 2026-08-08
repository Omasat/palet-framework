<?php

declare(strict_types=1);

namespace Palet\Framework\Support;

use Exception;

class Str
{
    public static function slug(string $title, string $separator = '-', string $language = 'en'): string
    {
        $map = [
            'Ş' => 's', 'ş' => 's', 'Ğ' => 'g', 'ğ' => 'g',
            'Ç' => 'c', 'ç' => 'c', 'İ' => 'i', 'ı' => 'i',
            'Ö' => 'o', 'ö' => 'o', 'Ü' => 'u', 'ü' => 'u',
        ];
        
        $title = str_replace(array_keys($map), array_values($map), $title);
        $title = mb_strtolower($title, 'UTF-8');
        $title = preg_replace('/[^\p{L}\p{N}]+/u', $separator, $title);
        $title = trim($title, $separator);

        return $title;
    }

    public static function camel(string $value): string
    {
        return lcfirst(static::studly($value));
    }

    public static function studly(string $value): string
    {
        $value = ucwords(str_replace(['-', '_'], ' ', $value));
        return str_replace(' ', '', $value);
    }

    public static function snake(string $value, string $delimiter = '_'): string
    {
        if (!ctype_lower($value)) {
            $value = preg_replace('/\s+/u', '', ucwords($value));
            $value = mb_strtolower(preg_replace('/(.)(?=[A-Z])/u', '$1' . $delimiter, $value), 'UTF-8');
        }

        return $value;
    }

    public static function uuid(): string
    {
        try {
            $data = random_bytes(16);
        } catch (Exception $e) {
            // Fallback if random_bytes is somehow unavailable
            $data = md5(uniqid((string)mt_rand(), true), true);
        }
        
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40); // set version to 0100
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80); // set bits 6-7 to 10
        
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }

    public static function contains(string $haystack, string|array $needles): bool
    {
        foreach ((array) $needles as $needle) {
            if ($needle !== '' && str_contains($haystack, $needle)) {
                return true;
            }
        }

        return false;
    }
}
