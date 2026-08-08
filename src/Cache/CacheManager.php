<?php

declare(strict_types=1);

namespace Palet\Framework\Cache;

use Palet\Framework\Contracts\Cache\CacheStoreInterface;
use Palet\Framework\Cache\Drivers\ArrayDriver;
use Palet\Framework\Cache\Drivers\NullDriver;
use Palet\Framework\Cache\Drivers\FileDriver;
use Palet\Framework\Cache\Drivers\RedisDriver;
use Palet\Framework\Cache\Drivers\MemcachedDriver;
use InvalidArgumentException;
use Palet\Framework\Contracts\Foundation\ApplicationInterface;

class CacheManager
{
    protected array $stores = [];
    protected array $customCreators = [];
    protected string $defaultDriver = 'array';
    protected ?ApplicationInterface $app = null;
    
    public function __construct(?ApplicationInterface $app = null)
    {
        $this->app = $app;
    }
    
    public function store(?string $name = null): CacheStoreInterface
    {
        $name = $name ?: $this->defaultDriver;
        
        if (!isset($this->stores[$name])) {
            $this->stores[$name] = $this->resolve($name);
        }
        
        return $this->stores[$name];
    }
    
    protected function resolve(string $name): CacheStoreInterface
    {
        if (isset($this->customCreators[$name])) {
            return $this->customCreators[$name]();
        }
        
        $driverMethod = 'create' . ucfirst($name) . 'Driver';
        
        if (method_exists($this, $driverMethod)) {
            return $this->{$driverMethod}();
        }
        
        throw new InvalidArgumentException("Cache driver [{$name}] is not supported.");
    }
    
    protected function createArrayDriver(): CacheStoreInterface
    {
        return new CacheRepository(new ArrayDriver());
    }

    protected function createNullDriver(): CacheStoreInterface
    {
        return new CacheRepository(new NullDriver());
    }

    protected function createFileDriver(): CacheStoreInterface
    {
        $path = $this->app ? $this->app->storagePath('framework/cache') : sys_get_temp_dir() . '/palet_cache';
        return new CacheRepository(new FileDriver($path));
    }
    
    protected function createRedisDriver(): CacheStoreInterface
    {
        $redis = new \Redis();
        $config = $this->app && $this->app->has('config') ? $this->app->make('config')->get('cache.stores.redis', []) : [];
        $host = $config['host'] ?? '127.0.0.1';
        $port = $config['port'] ?? 6379;
        
        $redis->connect($host, (int) $port);
        
        if (isset($config['password']) && $config['password'] !== '') {
            $redis->auth($config['password']);
        }
        
        return new CacheRepository(new RedisDriver($redis));
    }

    protected function createMemcachedDriver(): CacheStoreInterface
    {
        $memcached = new \Memcached();
        $config = $this->app && $this->app->has('config') ? $this->app->make('config')->get('cache.stores.memcached', []) : [];
        
        if (empty($memcached->getServerList())) {
            $host = $config['host'] ?? '127.0.0.1';
            $port = $config['port'] ?? 11211;
            $memcached->addServer($host, (int) $port);
        }
        
        return new CacheRepository(new MemcachedDriver($memcached));
    }
    
    public function extend(string $driver, \Closure $callback): static
    {
        $this->customCreators[$driver] = $callback;
        return $this;
    }
    
    public function __call(string $method, array $parameters)
    {
        return $this->store()->$method(...$parameters);
    }
}
