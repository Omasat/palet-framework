<?php

declare(strict_types=1);

namespace Palet\Framework\Database\Connection;

use Palet\Framework\Contracts\Database\ConnectionPoolInterface;
use PDO;

class ConnectionPool implements ConnectionPoolInterface
{
    protected ConnectionFactory $factory;
    
    /** @var array<string, array<PDO>> */
    protected array $pool = [];

    public function __construct(ConnectionFactory $factory)
    {
        $this->factory = $factory;
    }

    public function checkout(string $name, array $config): PDO
    {
        if (isset($this->pool[$name]) && count($this->pool[$name]) > 0) {
            return array_pop($this->pool[$name]);
        }

        return $this->factory->make($config);
    }

    public function checkin(string $name, PDO $connection): void
    {
        if (!isset($this->pool[$name])) {
            $this->pool[$name] = [];
        }
        
        $this->pool[$name][] = $connection;
    }
    
    public function flush(): void
    {
        $this->pool = [];
    }
}
