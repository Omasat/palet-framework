<?php

declare(strict_types=1);

namespace Tests\Generator\Scaffold;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Console\Commands\Generator\Scaffold\MakeScaffoldCommand;
use Palet\Framework\Console\Input\ArgvInput;
use Palet\Framework\Console\Output\ConsoleOutput;

class MakeScaffoldCommandTest extends TestCase
{
    public function test_command_dry_run()
    {
        $command = new MakeScaffoldCommand();
        
        // Use basic_entity blueprint for testing
        $input = new ArgvInput(['palet', 'make:scaffold', 'basic_entity', '--dry-run']);
        $output = new ConsoleOutput();
        $output->setBuffered(true);
        
        $exitCode = $command->run($input, $output);
        
        $this->assertEquals(0, $exitCode, "Command failed with output: " . $output->getBuffer());
        $this->assertStringContainsString('DRY RUN', $output->getBuffer());
    }
}
