<?php

declare(strict_types=1);

namespace Tests\Console;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Console\Input\ArgvInput;

class ArgvInputTest extends TestCase
{
    public function test_parses_arguments_and_options()
    {
        $argv = [
            'palet',            // script name
            'make:controller',  // first argument (command)
            'UserController',   // second argument
            '--force',          // long boolean option
            '--type=api',       // long value option
            '-v'                // short option
        ];

        $input = new ArgvInput($argv);
        
        $this->assertEquals('make:controller', $input->getFirstArgument());
        $this->assertEquals('make:controller', $input->getArgument('0'));
        $this->assertEquals('UserController', $input->getArgument('1'));
        
        $this->assertTrue($input->hasOption('force'));
        $this->assertTrue($input->getOption('force'));
        
        $this->assertTrue($input->hasOption('type'));
        $this->assertEquals('api', $input->getOption('type'));
        
        $this->assertTrue($input->hasOption('v'));
    }
}
