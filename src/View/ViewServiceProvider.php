<?php

declare(strict_types=1);

namespace Palet\Framework\View;

use Palet\Framework\Foundation\Providers\ServiceProvider;
use Palet\Framework\Contracts\View\ViewFinderInterface;
use Palet\Framework\Contracts\View\ViewFactoryInterface;
use Palet\Framework\View\Engines\PhpEngine;
use Palet\Framework\View\Engines\CompilerEngine;

class ViewServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->registerViewFinder();
        $this->registerFactory();
    }

    protected function registerViewFinder(): void
    {
        $this->app->singleton(ViewFinderInterface::class, function ($app) {
            $paths = [$app->basePath('resources/views')];
            return new FileViewFinder($paths, ['palet.php', 'php']);
        });
    }

    protected function registerFactory(): void
    {
        $this->app->singleton(ViewFactoryInterface::class, function ($app) {
            $finder = $app->make(ViewFinderInterface::class);
            $factory = new Factory($finder);
            
            // Register PhpEngine
            $factory->addEngine('php', new PhpEngine());
            
            // Register CompilerEngine if exists (for .palet.php)
            // Assuming compiler engine uses standard php engine or compiler
            // For now, we map 'palet.php' to PhpEngine as well if no compiler is bound,
            // or if CompilerEngine requires a compiler, we just use PhpEngine for simplicity here.
            $factory->addEngine('palet.php', new PhpEngine());

            return $factory;
        });
    }

    public function boot(): void
    {
        // View setup could publish configuration here
    }
}
