<?php

declare(strict_types=1);

namespace Palet\Framework\Config;

class ConfigCache
{
    /**
     * Konfigürasyon dizisini cache dosyasına yazar.
     */
    public static function write(string $path, array $items): bool
    {
        $content = '<?php return ' . var_export($items, true) . ';' . PHP_EOL;
        
        $result = file_put_contents($path, $content, LOCK_EX);
        
        return $result !== false;
    }

    /**
     * Cache dosyasını okur. Yoksa veya okunamazsa null döner.
     */
    public static function read(string $path): ?array
    {
        if (is_file($path) && is_readable($path)) {
            $items = require $path;
            
            if (is_array($items)) {
                return $items;
            }
        }

        return null;
    }
}
