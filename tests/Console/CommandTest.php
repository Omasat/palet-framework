<?php

declare(strict_types=1);

namespace Tests\Console;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Console\Command;
use Palet\Framework\Console\Input\ArgvInput;
use Palet\Framework\Console\Output\ConsoleOutput;

class TestCommand extends Command
{
    protected string $name = 'test:args';
    
    protected function execute(): int
    {
        if ($this->hasOption('fail')) {
            $this->error('Failed intentionally');
            return 1;
        }
        
        $arg1 = $this->argument('1');
        $this->info("Arg is: {$arg1}");
        
        return 0;
    }
}

class CommandTest extends TestCase
{
    public function test_command_helpers()
    {
        $command = new TestCommand();
        
        $input = new ArgvInput(['palet', 'test:args', 'Hello']);
        $output = new ConsoleOutput();
        $output->setBuffered(true);
        
        $exitCode = $command->run($input, $output);
        
        $this->assertEquals(0, $exitCode);
        $this->assertStringContainsString('Arg is: Hello', $output->getBuffer());
    }

    public function test_command_error_helper()
    {
        $command = new TestCommand();
        
        $input = new ArgvInput(['palet', 'test:args', '--fail']);
        $output = new ConsoleOutput();
        $output->setBuffered(true);
        
        $exitCode = $command->run($input, $output);
        
        $this->assertEquals(1, $exitCode);
        $this->assertStringContainsString('Failed intentionally', $output->getBuffer());
        $this->assertStringContainsString("\033[31m", $output->getBuffer()); // Red error color
    }
}
