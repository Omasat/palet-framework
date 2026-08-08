<?php

declare(strict_types=1);

namespace Tests\Config;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Config\ConfigMerger;

class ConfigMergerTest extends TestCase
{
    public function test_merges_two_configuration_arrays()
    {
        $original = [
            'app' => [
                'name' => 'OriginalApp',
                'env' => 'production'
            ],
            'database' => [
                'connections' => [
                    'mysql' => ['host' => '127.0.0.1']
                ]
            ]
        ];

        $package = [
            'app' => [
                'name' => 'PackageApp',
                'debug' => true,
            ],
            'database' => [
                'connections' => [
                    'mysql' => ['port' => 3306],
                    'sqlite' => ['database' => ':memory:']
                ]
            ],
            'new_key' => 'value'
        ];

        // Merge operation (original has priority, missing keys are added from package)
        $merged = ConfigMerger::merge($original, $package);

        // Original is preserved if it existed
        $this->assertEquals('OriginalApp', $merged['app']['name']);
        
        // Missing keys are added
        $this->assertTrue($merged['app']['debug']);
        
        // Deeply nested keys are merged
        $this->assertEquals('127.0.0.1', $merged['database']['connections']['mysql']['host']);
        $this->assertEquals(3306, $merged['database']['connections']['mysql']['port']);
        $this->assertEquals(':memory:', $merged['database']['connections']['sqlite']['database']);
        
        // Top level new keys are added
        $this->assertEquals('value', $merged['new_key']);
    }
}
