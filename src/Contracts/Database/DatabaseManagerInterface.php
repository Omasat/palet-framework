<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Database;

interface DatabaseManagerInterface
{
    /**
     * Get a database connection instance.
     */
    public function connection(?string $name = null): ConnectionInterface;

    /**
     * Reconnect to the given database.
     */
    public function reconnect(?string $name = null): ConnectionInterface;

    /**
     * Disconnect from the given database.
     */
    public function disconnect(?string $name = null): void;

    /**
     * Get the default connection name.
     */
    public function getDefaultConnection(): string;

    /**
     * Set the default connection name.
     */
    public function setDefaultConnection(string $name): void;
}
