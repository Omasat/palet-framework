<?php

declare(strict_types=1);

namespace Tests\Console;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Console\Application;
use Palet\Framework\Console\Command;
use Palet\Framework\Console\Input\ArgvInput;
use Palet\Framework\Console\Output\ConsoleOutput;
use Palet\Framework\Console\Exceptions\CommandNotFoundException;

class DummyCommand extends Command
{
    protected string $name = 'test:cmd';
    
    protected function execute(): int
    {
        $this->info('Test executed!');
        return 0;
    }
}

class ApplicationTest extends TestCase
{
    public function test_runs_command_successfully()
    {
        $app = new Application();
        $app->add(new DummyCommand());
        
        $input = new ArgvInput(['palet', 'test:cmd']);
        $output = new ConsoleOutput();
        $output->setBuffered(true);
        
        $exitCode = $app->run($input, $output);
        
        $this->assertEquals(0, $exitCode);
        
        // Assert info tag converted to green ANSI
        $this->assertStringContainsString('Test executed!', $output->getBuffer());
        $this->assertStringContainsString("\033[32m", $output->getBuffer());
    }

    public function test_throws_exception_on_unknown_command()
    {
        $app = new Application();
        
        $input = new ArgvInput(['palet', 'unknown:cmd']);
        $output = new ConsoleOutput();
        $output->setBuffered(true);
        
        $this->expectException(CommandNotFoundException::class);
        $app->run($input, $output);
    }
}
