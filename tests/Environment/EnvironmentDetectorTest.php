<?php

declare(strict_types=1);

namespace Tests\Environment;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Environment\EnvironmentDetector;
use Palet\Framework\Environment\EnvRepository;

class EnvironmentDetectorTest extends TestCase
{
    public function test_detects_environment()
    {
        $env = new EnvRepository(['APP_ENV' => 'Local']);
        
        $this->assertEquals('local', EnvironmentDetector::detect($env));
    }

    public function test_returns_default_environment()
    {
        $env = new EnvRepository();
        
        $this->assertEquals('production', EnvironmentDetector::detect($env));
        $this->assertEquals('testing', EnvironmentDetector::detect($env, 'testing'));
    }
}
