<?php

declare(strict_types=1);

namespace Palet\Framework\Database\Drivers;

use Palet\Framework\Contracts\Database\DriverInterface;
use PDO;
use Exception;

abstract class AbstractDriver implements DriverInterface
{
    public function connect(array $config): PDO
    {
        $dsn = $this->getDsn($config);
        $options = $this->getOptions($config);

        try {
            return new PDO($dsn, $config['username'] ?? null, $config['password'] ?? null, $options);
        } catch (Exception $e) {
            // Mask credentials in exception
            throw new \PDOException("Could not connect to the database. " . $e->getMessage(), (int)$e->getCode());
        }
    }

    protected function getOptions(array $config): array
    {
        $options = $config['options'] ?? [];
        
        // Default PDO options for the framework
        $options[PDO::ATTR_ERRMODE] = PDO::ERRMODE_EXCEPTION;
        $options[PDO::ATTR_DEFAULT_FETCH_MODE] = PDO::FETCH_OBJ;
        $options[PDO::ATTR_EMULATE_PREPARES] = false;
        $options[PDO::ATTR_STRINGIFY_FETCHES] = false;
        
        return $options;
    }
}
