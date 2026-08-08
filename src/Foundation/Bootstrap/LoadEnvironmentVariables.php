<?php

declare(strict_types=1);

namespace Palet\Framework\Foundation\Bootstrap;

use Palet\Framework\Contracts\Foundation\ApplicationInterface;
use Palet\Framework\Foundation\FrameworkState;

use Palet\Framework\Environment\EnvLoader;
use Palet\Framework\Contracts\Config\EnvironmentInterface;

class LoadEnvironmentVariables implements BootstrapperInterface
{
    public function bootstrap(ApplicationInterface $app): void
    {
        if (method_exists($app, 'setState')) {
            $app->setState(FrameworkState::Bootstrapping);
        }

        $cachePath = method_exists($app, 'bootstrapPath') ? $app->bootstrapPath('cache/env.php') : '';
        
        $items = null;
        if ($cachePath !== '' && file_exists($cachePath)) {
            $items = require $cachePath;
        }

        if (is_array($items)) {
            $repository = new \Palet\Framework\Environment\EnvRepository();
            foreach ($items as $key => $value) {
                $repository->set($key, $value);
            }
        } else {
            // basePath üzerinden EnvLoader'ı başlat ve env dosyasını yükle
            $loader = new EnvLoader(method_exists($app, 'basePath') ? $app->basePath() : '');
            $repository = $loader->load();
        }

        // Container'a kaydet
        if (method_exists($app, 'instance')) {
            $app->instance(EnvironmentInterface::class, $repository);
            $app->instance('env', $repository);
        }
    }
}
