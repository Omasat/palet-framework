<?php

declare(strict_types=1);

namespace Tests\Diagnostics;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Diagnostics\HealthCheckEngine;
use Palet\Framework\Contracts\Diagnostics\HealthCheckInterface;
use RuntimeException;

class HealthCheckEngineTest extends TestCase
{
    public function test_engine_runs_checks_and_collects_results()
    {
        $engine = new HealthCheckEngine();
        
        $passCheck = new class implements HealthCheckInterface {
            public function getName(): string { return 'Pass'; }
            public function getDescription(): string { return 'Passing test'; }
            public function check(): bool { return true; }
            public function getErrorMessage(): ?string { return null; }
        };
        
        $failCheck = new class implements HealthCheckInterface {
            public function getName(): string { return 'Fail'; }
            public function getDescription(): string { return 'Failing test'; }
            public function check(): bool { return false; }
            public function getErrorMessage(): ?string { return 'Failed intentionally'; }
        };
        
        $engine->register($passCheck);
        $engine->register($failCheck);
        
        $results = $engine->runAll();
        
        $this->assertCount(2, $results);
        $this->assertTrue($results[0]['passed']);
        $this->assertFalse($results[1]['passed']);
        $this->assertEquals('Failed intentionally', $results[1]['error']);
    }

    public function test_engine_catches_exceptions()
    {
        $engine = new HealthCheckEngine();
        
        $exceptionCheck = new class implements HealthCheckInterface {
            public function getName(): string { return 'Exception'; }
            public function getDescription(): string { return ''; }
            public function check(): bool { throw new RuntimeException('Crash!'); }
            public function getErrorMessage(): ?string { return null; }
        };
        
        $engine->register($exceptionCheck);
        $results = $engine->runAll();
        
        $this->assertCount(1, $results);
        $this->assertFalse($results[0]['passed']);
        $this->assertEquals('Crash!', $results[0]['error']);
    }
}
