<?php

declare(strict_types=1);

namespace Palet\Framework\Session;

use InvalidArgumentException;
use SessionHandlerInterface;
use Closure;
use Palet\Framework\Contracts\Session\SessionInterface;
use Palet\Framework\Session\Drivers\ArraySessionDriver;
use Palet\Framework\Session\Drivers\FileSessionDriver;

class SessionManager
{
    protected array $customCreators = [];
    protected string $defaultDriver = 'array';
    protected ?SessionInterface $store = null;
    
    public function driver(?string $driver = null): SessionInterface
    {
        $driver = $driver ?: $this->defaultDriver;
        
        if ($this->store === null) {
            $this->store = $this->buildSession($this->createDriver($driver));
        }
        
        return $this->store;
    }
    
    protected function buildSession(SessionHandlerInterface $handler): SessionInterface
    {
        return new Store('palet_session', $handler);
    }
    
    protected function createDriver(string $driver): SessionHandlerInterface
    {
        if (isset($this->customCreators[$driver])) {
            return $this->customCreators[$driver]();
        }
        
        $method = 'create' . ucfirst($driver) . 'Driver';
        
        if (method_exists($this, $method)) {
            return $this->$method();
        }
        
        throw new InvalidArgumentException("Session driver [$driver] is not supported.");
    }
    
    protected function createArrayDriver(): SessionHandlerInterface
    {
        return new ArraySessionDriver(10);
    }
    
    protected function createFileDriver(): SessionHandlerInterface
    {
        return new FileSessionDriver(sys_get_temp_dir() . '/palet_sessions', 10);
    }

    public function extend(string $driver, Closure $callback): static
    {
        $this->customCreators[$driver] = $callback;
        return $this;
    }

    public function __call(string $method, array $parameters)
    {
        return $this->driver()->$method(...$parameters);
    }
}
