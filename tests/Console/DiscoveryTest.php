<?php

declare(strict_types=1);

namespace Tests\Console;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Console\Discovery\CommandScanner;
use Palet\Framework\Console\Discovery\CommandMetadata;

class DiscoveryTest extends TestCase
{
    public function test_discovers_commands_with_attributes()
    {
        $scanner = new CommandScanner();
        $commands = $scanner->scan([__DIR__ . '/Fixtures']);
        
        $this->assertArrayHasKey('fixture:test', $commands);
        $this->assertInstanceOf(CommandMetadata::class, $commands['fixture:test']);
        $this->assertEquals('A test fixture command', $commands['fixture:test']->description);
        $this->assertFalse($commands['fixture:test']->hidden);
    }

    public function test_discovers_hidden_commands()
    {
        $scanner = new CommandScanner();
        $commands = $scanner->scan([__DIR__ . '/Fixtures']);
        
        $this->assertArrayHasKey('fixture:hidden', $commands);
        $this->assertTrue($commands['fixture:hidden']->hidden);
    }

    public function test_discovers_legacy_commands()
    {
        $scanner = new CommandScanner();
        $commands = $scanner->scan([__DIR__ . '/Fixtures']);
        
        $this->assertArrayHasKey('fixture:legacy', $commands);
        $this->assertEquals('A legacy command without attributes', $commands['fixture:legacy']->description);
    }
}
