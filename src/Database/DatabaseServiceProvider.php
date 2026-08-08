<?php

declare(strict_types=1);

namespace Palet\Framework\Database;

use Palet\Framework\Foundation\Providers\ServiceProvider;
use Palet\Framework\Contracts\Database\DatabaseManagerInterface;
use Palet\Framework\Database\Connection\DatabaseManager;
use Palet\Framework\Database\Connection\ConnectionPool;
use Palet\Framework\Database\Connection\ConnectionHealthMonitor;

class DatabaseServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Connection Factory
        $this->app->singleton(\Palet\Framework\Database\Connection\ConnectionFactory::class, function ($app) {
            return new \Palet\Framework\Database\Connection\ConnectionFactory();
        });

        // Connection Pool
        $this->app->singleton(ConnectionPool::class, function ($app) {
            return new ConnectionPool($app->make(\Palet\Framework\Database\Connection\ConnectionFactory::class));
        });

        // Connection Health Monitor
        $this->app->singleton(ConnectionHealthMonitor::class, function ($app) {
            return new ConnectionHealthMonitor();
        });

        // Database Manager
        $this->app->singleton(DatabaseManagerInterface::class, function ($app) {
            // ConfigRepository gives us configuration values
            $config = $app->make('config')->get('database', [
                'default' => 'mysql',
                'connections' => [
                    'mysql' => [
                        'driver' => 'mysql',
                        'host' => '127.0.0.1',
                        'port' => '3306',
                        'database' => 'palet',
                        'username' => 'root',
                        'password' => '',
                    ]
                ]
            ]);
            
            return new DatabaseManager(
                $config,
                $app->make(ConnectionPool::class),
                $app->make(ConnectionHealthMonitor::class)
            );
        });

        // Alias 'db' to DatabaseManager
        $this->app->alias(DatabaseManagerInterface::class, 'db');
    }

    public function boot(): void
    {
        // Herhangi bir boot işlemi (örneğin event dinleyicileri eklenebilir)
    }
}
