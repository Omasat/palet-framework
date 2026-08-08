<?php

declare(strict_types=1);

namespace Tests\Diagnostics;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Console\Commands\Diagnostics\DoctorCommand;
use Palet\Framework\Console\Input\ArgvInput;
use Palet\Framework\Console\Output\ConsoleOutput;

class DoctorCommandTest extends TestCase
{
    public function test_doctor_command_runs()
    {
        // Change cwd to framework root to make checks pass/fail predictably
        $cwd = getcwd();
        chdir(dirname(__DIR__, 2));
        
        $command = new DoctorCommand();
        $input = new ArgvInput(['palet', 'doctor']);
        $output = new ConsoleOutput();
        $output->setBuffered(true);
        
        $exitCode = $command->run($input, $output);
        
        // It might fail or pass depending on if .env exists in framework root
        // The important part is that it runs and outputs the table
        $buffer = $output->getBuffer();
        
        $this->assertStringContainsString('Framework Diagnostics Report', $buffer);
        $this->assertStringContainsString('PHP Version', $buffer);
        $this->assertStringContainsString('File Permissions', $buffer);
        
        chdir($cwd);
    }
}
