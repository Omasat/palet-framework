<?php

declare(strict_types=1);

namespace Tests\Config;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Config\ConfigRepository;

class ConfigRepositoryTest extends TestCase
{
    protected array $config;

    protected function setUp(): void
    {
        $this->config = [
            'app' => [
                'name' => 'Palet',
                'debug' => true,
            ],
            'database' => [
                'default' => 'mysql',
                'connections' => [
                    'mysql' => [
                        'host' => '127.0.0.1',
                    ],
                ],
            ],
        ];
    }

    public function test_can_get_values_with_dot_notation()
    {
        $repository = new ConfigRepository($this->config);

        $this->assertEquals('Palet', $repository->get('app.name'));
        $this->assertTrue($repository->get('app.debug'));
        $this->assertEquals('127.0.0.1', $repository->get('database.connections.mysql.host'));
        $this->assertEquals('mysql', $repository->get('database.default'));
    }

    public function test_get_returns_default_if_not_found()
    {
        $repository = new ConfigRepository($this->config);

        $this->assertEquals('default_value', $repository->get('app.non_existent', 'default_value'));
        $this->assertNull($repository->get('app.non_existent'));
    }

    public function test_can_check_if_value_exists_with_dot_notation()
    {
        $repository = new ConfigRepository($this->config);

        $this->assertTrue($repository->has('app.name'));
        $this->assertTrue($repository->has('database.connections.mysql.host'));
        $this->assertFalse($repository->has('app.non_existent'));
        $this->assertFalse($repository->has('database.connections.pgsql'));
    }

    public function test_can_set_values_with_dot_notation()
    {
        $repository = new ConfigRepository($this->config);

        $repository->set('app.timezone', 'UTC');
        $repository->set('database.connections.sqlite.database', 'database.sqlite');

        $this->assertEquals('UTC', $repository->get('app.timezone'));
        $this->assertEquals('database.sqlite', $repository->get('database.connections.sqlite.database'));
    }
}
