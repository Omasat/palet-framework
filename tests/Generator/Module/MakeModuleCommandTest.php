<?php

declare(strict_types=1);

namespace Tests\Generator\Module;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Console\Commands\Generator\Module\MakeModuleCommand;
use Palet\Framework\Console\Input\ArgvInput;
use Palet\Framework\Console\Output\ConsoleOutput;

class MakeModuleCommandTest extends TestCase
{
    public function test_command_dry_run()
    {
        $command = new MakeModuleCommand();
        
        $input = new ArgvInput(['palet', 'make:module', 'TestModule', '--dry-run']);
        $output = new ConsoleOutput();
        $output->setBuffered(true);
        
        $exitCode = $command->run($input, $output);
        
        $this->assertEquals(0, $exitCode);
        $this->assertStringContainsString('DRY RUN', $output->getBuffer());
    }
}
