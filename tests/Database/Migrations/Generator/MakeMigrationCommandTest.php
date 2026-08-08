<?php

declare(strict_types=1);

namespace Tests\Database\Migrations\Generator;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Console\Commands\Database\Migrations\MakeMigrationCommand;
use Palet\Framework\Console\Input\ArgvInput;
use Palet\Framework\Console\Output\ConsoleOutput;

class MakeMigrationCommandTest extends TestCase
{
    public function test_command_dry_run_does_not_create_file()
    {
        $command = new MakeMigrationCommand();
        
        $input = new ArgvInput(['palet', 'make:migration', 'create_dry_run_table', '--dry-run']);
        $output = new ConsoleOutput();
        $output->setBuffered(true);
        
        $exitCode = $command->run($input, $output);
        
        $this->assertEquals(0, $exitCode);
        $this->assertStringContainsString('DRY RUN', $output->getBuffer());
    }
}
