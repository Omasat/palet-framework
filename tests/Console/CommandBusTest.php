<?php

declare(strict_types=1);

namespace Tests\Console;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Console\Bus\CommandBus;
use Palet\Framework\Console\Bus\CommandResolver;
use Palet\Framework\Console\Input\ArgvInput;
use Palet\Framework\Console\Output\ConsoleOutput;
use Palet\Framework\Events\EventDispatcher;
use Palet\Framework\Console\Events\CommandResolving;
use Palet\Framework\Console\Events\CommandResolved;
use Palet\Framework\Console\Events\CommandExecuting;
use Palet\Framework\Console\Events\CommandExecuted;
use Palet\Framework\Console\Exceptions\CommandNotFoundException;
use Tests\Console\Fixtures\FixtureCommand;

require_once __DIR__ . '/Fixtures/FixtureCommand.php';

class CommandBusTest extends TestCase
{
    public function test_bus_dispatches_command_and_fires_events()
    {
        $resolver = new CommandResolver();
        $resolver->register('fixture:test', new FixtureCommand());
        
        $bus = new CommandBus($resolver);
        
        $dispatcher = new EventDispatcher();
        $dispatchedEvents = [];
        
        $dispatcher->listen(CommandResolving::class, function($e) use (&$dispatchedEvents) { $dispatchedEvents[] = 'resolving'; });
        $dispatcher->listen(CommandResolved::class, function($e) use (&$dispatchedEvents) { $dispatchedEvents[] = 'resolved'; });
        $dispatcher->listen(CommandExecuting::class, function($e) use (&$dispatchedEvents) { $dispatchedEvents[] = 'executing'; });
        $dispatcher->listen(CommandExecuted::class, function($e) use (&$dispatchedEvents) { $dispatchedEvents[] = 'executed'; });
        
        $bus->setEventDispatcher($dispatcher);
        
        $input = new ArgvInput(['palet', 'fixture:test']);
        $output = new ConsoleOutput();
        
        $exitCode = $bus->dispatch('fixture:test', $input, $output);
        
        $this->assertEquals(0, $exitCode);
        $this->assertEquals(['resolving', 'resolved', 'executing', 'executed'], $dispatchedEvents);
    }

    public function test_bus_throws_on_unknown_command()
    {
        $resolver = new CommandResolver();
        $bus = new CommandBus($resolver);
        
        $input = new ArgvInput(['palet', 'unknown']);
        $output = new ConsoleOutput();
        
        $this->expectException(CommandNotFoundException::class);
        $bus->dispatch('unknown', $input, $output);
    }
}
