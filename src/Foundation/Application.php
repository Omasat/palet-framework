<?php

declare(strict_types=1);

namespace Palet\Framework\Foundation;

use Palet\Framework\Container\Container;
use Palet\Framework\Contracts\Foundation\ApplicationInterface;
use Palet\Framework\Contracts\Support\ServiceProviderInterface;

/**
 * Framework'ün temel uygulama yöneticisi.
 */
class Application extends Container implements ApplicationInterface
{
    /**
     * Framework'ün şu anki durumu.
     */
    private FrameworkState $state = FrameworkState::Booting;

    /**
     * Temel uygulama dizini.
     */
    private string $basePath;

    /**
     * Boot edilip edilmediğini tutar.
     */
    private bool $booted = false;

    /**
     * Application constructor.
     *
     * @param string $basePath Uygulamanın kök dizini (örn: dirname(__DIR__))
     */
    public function __construct(string $basePath)
    {
        parent::__construct();

        EnvironmentValidator::validate();

        $this->setBasePath($basePath);
        $this->registerBaseBindings();
    }

    /**
     * Set the base path for the application.
     */
    public function setBasePath(string $basePath): self
    {
        $this->basePath = rtrim($basePath, '\/');
        $this->bindPathsInContainer();

        return $this;
    }

    /**
     * Bind all of the application paths in the container.
     */
    protected function bindPathsInContainer(): void
    {
        $this->instance('path.base', $this->basePath());
        $this->instance('path.app', $this->appPath());
        $this->instance('path.config', $this->configPath());
        $this->instance('path.storage', $this->storagePath());
        $this->instance('path.public', $this->publicPath());
        $this->instance('path.resources', $this->resourcesPath());
        $this->instance('path.routes', $this->routesPath());
        $this->instance('path.bootstrap', $this->bootstrapPath());
        $this->instance('path.lang', $this->langPath());
        $this->instance('path.vendor', $this->vendorPath());
    }

    /**
     * Register the basic bindings into the container.
     */
    protected function registerBaseBindings(): void
    {
        static::setInstance($this);
        $this->instance('app', $this);
        $this->instance(Container::class, $this);
        $this->instance(ApplicationInterface::class, $this);
    }

    /**
     * Framework versiyonunu döndürür.
     */
    public function version(): string
    {
        return Version::getVersion();
    }

    /**
     * Get the base path of the Laravel installation.
     */
    public function basePath(string $path = ''): string
    {
        return $this->basePath . ($path !== '' ? DIRECTORY_SEPARATOR . $path : '');
    }

    public function appPath(string $path = ''): string
    {
        return $this->basePath . DIRECTORY_SEPARATOR . 'app' . ($path !== '' ? DIRECTORY_SEPARATOR . $path : '');
    }

    public function configPath(string $path = ''): string
    {
        return $this->basePath . DIRECTORY_SEPARATOR . 'config' . ($path !== '' ? DIRECTORY_SEPARATOR . $path : '');
    }

    public function storagePath(string $path = ''): string
    {
        return $this->basePath . DIRECTORY_SEPARATOR . 'storage' . ($path !== '' ? DIRECTORY_SEPARATOR . $path : '');
    }

    public function publicPath(string $path = ''): string
    {
        return $this->basePath . DIRECTORY_SEPARATOR . 'public' . ($path !== '' ? DIRECTORY_SEPARATOR . $path : '');
    }

    public function resourcesPath(string $path = ''): string
    {
        return $this->basePath . DIRECTORY_SEPARATOR . 'resources' . ($path !== '' ? DIRECTORY_SEPARATOR . $path : '');
    }

    public function routesPath(string $path = ''): string
    {
        return $this->basePath . DIRECTORY_SEPARATOR . 'routes' . ($path !== '' ? DIRECTORY_SEPARATOR . $path : '');
    }

    public function bootstrapPath(string $path = ''): string
    {
        return $this->basePath . DIRECTORY_SEPARATOR . 'bootstrap' . ($path !== '' ? DIRECTORY_SEPARATOR . $path : '');
    }

    public function langPath(string $path = ''): string
    {
        return $this->basePath . DIRECTORY_SEPARATOR . 'lang' . ($path !== '' ? DIRECTORY_SEPARATOR . $path : '');
    }

    public function vendorPath(string $path = ''): string
    {
        return $this->basePath . DIRECTORY_SEPARATOR . 'vendor' . ($path !== '' ? DIRECTORY_SEPARATOR . $path : '');
    }

    /**
     * Determine if the application is running in the console.
     */
    public function runningInConsole(): bool
    {
        return Runtime::isCli();
    }

    /**
     * Get the current framework state.
     */
    public function getState(): FrameworkState
    {
        return $this->state;
    }

    /**
     * Set the current framework state.
     */
    public function setState(FrameworkState $state): void
    {
        $this->state = $state;
    }

    /**
     * Tüm servis sağlayıcıların tutulduğu dizi.
     * @var ServiceProviderInterface[]
     */
    protected array $serviceProviders = [];

    /**
     * Yüklenmiş servis sağlayıcılarının sınıfları.
     * @var array<string, bool>
     */
    protected array $loadedProviders = [];

    /**
     * Register a service provider with the application.
     */
    public function register(string|ServiceProviderInterface $provider, bool $force = false): ServiceProviderInterface
    {
        if (is_string($provider)) {
            $provider = $this->resolveProvider($provider);
        }

        $class = get_class($provider);

        if (array_key_exists($class, $this->loadedProviders) && !$force) {
            return $this->getProvider($class);
        }

        if (method_exists($provider, 'register')) {
            $provider->register();
        }

        $this->markAsRegistered($provider);

        if ($this->booted && method_exists($provider, 'boot')) {
            $provider->boot();
        }

        return $provider;
    }

    /**
     * Resolve a service provider instance from the class name.
     */
    protected function resolveProvider(string $provider): ServiceProviderInterface
    {
        return new $provider($this);
    }

    /**
     * Mark the given provider as registered.
     */
    protected function markAsRegistered(ServiceProviderInterface $provider): void
    {
        $this->serviceProviders[] = $provider;
        $this->loadedProviders[get_class($provider)] = true;
    }

    /**
     * Get a registered provider instance by class name.
     */
    public function getProvider(string $provider): ?ServiceProviderInterface
    {
        return array_values(array_filter($this->serviceProviders, fn($p) => $p instanceof $provider))[0] ?? null;
    }

    /**
     * Register all of the configured providers.
     */
    public function registerConfiguredProviders(): void
    {
        $providers = $this->make('config')->get('app.providers', []);

        $repository = new \Palet\Framework\Foundation\Providers\ProviderRepository($this);
        $repository->load($providers);
    }

    /**
     * Boot the application's service providers.
     */
    public function boot(): void
    {
        if ($this->booted) {
            return;
        }

        $this->setState(FrameworkState::BootingProviders);

        array_walk($this->serviceProviders, function (ServiceProviderInterface $p) {
            if (method_exists($p, 'boot')) {
                $p->boot();
            }
        });

        $this->booted = true;
        $this->setState(FrameworkState::Ready);
    }

    /**
     * Resolve the given type from the container.
     */
    protected function resolve(string $abstract, array $parameters = []): mixed
    {
        if (isset($this->deferredServices[$abstract]) && !isset($this->instances[$abstract])) {
            $this->loadDeferredProvider($abstract);
        }

        return parent::resolve($abstract, $parameters);
    }

    /**
     * Load the provider for the given deferred service.
     */
    protected function loadDeferredProvider(string $service): void
    {
        if (!isset($this->deferredProviderRepository)) {
            $this->deferredProviderRepository = new \Palet\Framework\Foundation\Providers\DeferredProviderRepository($this);
            $this->deferredProviderRepository->setDeferredServices($this->deferredServices);
        }

        $this->deferredProviderRepository->load($service);
    }

    /** @var array<string, string> */
    protected array $deferredServices = [];

    /**
     * Set the application's deferred services.
     */
    public function setDeferredServices(array $services): void
    {
        $this->deferredServices = $services;
        if (isset($this->deferredProviderRepository)) {
            $this->deferredProviderRepository->setDeferredServices($services);
        }
    }
}
