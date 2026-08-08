<?php

declare(strict_types=1);

namespace Palet\Framework\Support;

class Arr
{
    public static function get(array $array, string|int $key, mixed $default = null): mixed
    {
        if (array_key_exists($key, $array)) {
            return $array[$key];
        }

        if (str_contains((string)$key, '.')) {
            foreach (explode('.', (string)$key) as $segment) {
                if (is_array($array) && array_key_exists($segment, $array)) {
                    $array = $array[$segment];
                } else {
                    return $default;
                }
            }
            return $array;
        }

        return $default;
    }

    public static function set(array &$array, string $key, mixed $value): array
    {
        $keys = explode('.', $key);

        foreach ($keys as $i => $segment) {
            if (count($keys) === 1) {
                break;
            }
            unset($keys[$i]);

            if (!isset($array[$segment]) || !is_array($array[$segment])) {
                $array[$segment] = [];
            }
            $array = &$array[$segment];
        }

        $array[array_shift($keys)] = $value;

        return $array;
    }

    public static function has(array $array, string|array $keys): bool
    {
        $keys = (array) $keys;

        if (!$array || $keys === []) {
            return false;
        }

        foreach ($keys as $key) {
            $subKeyArray = $array;

            if (array_key_exists($key, $array)) {
                continue;
            }

            foreach (explode('.', $key) as $segment) {
                if (is_array($subKeyArray) && array_key_exists($segment, $subKeyArray)) {
                    $subKeyArray = $subKeyArray[$segment];
                } else {
                    return false;
                }
            }
        }

        return true;
    }
}
