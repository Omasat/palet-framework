<?php

declare(strict_types=1);

namespace Tests\Foundation;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Foundation\Runtime;
use Palet\Framework\Foundation\Version;

class RuntimeTest extends TestCase
{
    public function test_runtime_returns_correct_php_version()
    {
        $this->assertEquals(PHP_VERSION, Runtime::phpVersion());
    }

    public function test_runtime_returns_os()
    {
        $this->assertEquals(PHP_OS_FAMILY, Runtime::os());
    }

    public function test_runtime_returns_memory_usage()
    {
        $this->assertIsInt(Runtime::memoryUsage());
        $this->assertGreaterThan(0, Runtime::memoryUsage());
    }

    public function test_runtime_returns_cli_status()
    {
        // PHPUnit runs in CLI mode usually, unless explicitly tested differently.
        $this->assertTrue(Runtime::isCli());
    }
}
