<?php

declare(strict_types=1);

namespace Palet\Framework\Foundation\Bootstrap;

use Palet\Framework\Contracts\Foundation\ApplicationInterface;

use Palet\Framework\Config\ConfigLoader;
use Palet\Framework\Config\ConfigRepository;
use Palet\Framework\Contracts\Config\ConfigInterface;
use Palet\Framework\Config\ConfigCache;

class LoadConfiguration implements BootstrapperInterface
{
    public function bootstrap(ApplicationInterface $app): void
    {
        $items = [];
        $isCached = false;

        $cachePath = method_exists($app, 'bootstrapPath') 
            ? $app->bootstrapPath('cache/config.php') 
            : '';

        // Eğer cache dosyası varsa onu yükle (Performans)
        if ($cachePath !== '' && file_exists($cachePath)) {
            $cachedItems = ConfigCache::read($cachePath);
            if ($cachedItems !== null) {
                $items = $cachedItems;
                $isCached = true;
            }
        }

        // Cache yoksa diskten dosyaları tara ve yükle
        if (!$isCached) {
            $configPath = method_exists($app, 'configPath') ? $app->configPath() : '';
            $loader = new ConfigLoader();
            $items = $loader->load($configPath);
        }

        $repository = new ConfigRepository($items);

        // Uygulamanın timezone ve encoding ayarlarını config üzerinden başlatabiliriz
        if (isset($items['app']['timezone'])) {
            date_default_timezone_set($items['app']['timezone']);
        }
        
        if (isset($items['app']['encoding'])) {
            mb_internal_encoding($items['app']['encoding']);
        }

        if (method_exists($app, 'instance')) {
            $app->instance(ConfigInterface::class, $repository);
            $app->instance('config', $repository);
        }
    }
}
