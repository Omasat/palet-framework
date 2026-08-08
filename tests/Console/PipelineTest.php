<?php

declare(strict_types=1);

namespace Tests\Console;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Console\Pipeline\CommandPipeline;
use Palet\Framework\Console\Command;
use Palet\Framework\Contracts\Console\CommandMiddlewareInterface;
use Palet\Framework\Contracts\Console\CommandInterface;
use Palet\Framework\Contracts\Console\InputInterface;
use Palet\Framework\Contracts\Console\OutputInterface;
use Palet\Framework\Console\Input\ArgvInput;
use Palet\Framework\Console\Output\ConsoleOutput;
use Closure;

class LoggingMiddleware implements CommandMiddlewareInterface
{
    public function handle(CommandInterface $command, Closure $next, InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Before Execution');
        $exitCode = $next($command);
        $output->writeln('After Execution');
        return $exitCode;
    }
}

class PipelineCommand extends Command
{
    protected string $name = 'pipeline:test';
    
    protected function execute(): int
    {
        $this->output->writeln('Executing Command');
        return 0;
    }
}

class PipelineTest extends TestCase
{
    public function test_pipeline_wraps_command_execution()
    {
        $command = new PipelineCommand();
        $input = new ArgvInput(['palet', 'pipeline:test']);
        $output = new ConsoleOutput();
        $output->setBuffered(true);
        
        $pipeline = new CommandPipeline($input, $output);
        
        $exitCode = $pipeline->send($command)
            ->through([LoggingMiddleware::class])
            ->then(function ($cmd, $in, $out) {
                return $cmd->run($in, $out);
            });
            
        $this->assertEquals(0, $exitCode);
        
        $buffer = $output->getBuffer();
        $this->assertStringContainsString('Before Execution', $buffer);
        $this->assertStringContainsString('Executing Command', $buffer);
        $this->assertStringContainsString('After Execution', $buffer);
        
        // Assert order
        $posBefore = strpos($buffer, 'Before Execution');
        $posExec = strpos($buffer, 'Executing Command');
        $posAfter = strpos($buffer, 'After Execution');
        
        $this->assertTrue($posBefore < $posExec);
        $this->assertTrue($posExec < $posAfter);
    }
}
