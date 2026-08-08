<?php

declare(strict_types=1);

namespace Palet\Framework\Config;

class ConfigMerger
{
    /**
     * İki konfigürasyon dizisini (Package default ve User custom) derinlemesine (recursive) birleştirir.
     * Uygulama konfigürasyonu her zaman önceliklidir.
     */
    public static function merge(array $original, array $merging): array
    {
        $merged = $original;

        foreach ($merging as $key => $value) {
            if (is_array($value) && array_key_exists($key, $merged) && is_array($merged[$key])) {
                $merged[$key] = self::merge($merged[$key], $value);
            } else {
                // Eğer key orijinal dizide (uygulama yapılandırması) mevcut değilse ekle.
                if (!array_key_exists($key, $merged)) {
                    $merged[$key] = $value;
                }
            }
        }

        return $merged;
    }
}
