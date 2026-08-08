<?php

declare(strict_types=1);

namespace Palet\Framework\Console\Bus;

use Palet\Framework\Contracts\Console\CommandBusInterface;
use Palet\Framework\Contracts\Console\InputInterface;
use Palet\Framework\Contracts\Console\OutputInterface;
use Palet\Framework\Contracts\Console\CommandResolverInterface;
use Palet\Framework\Contracts\Events\EventDispatcherInterface;
use Palet\Framework\Console\Pipeline\CommandPipeline;
use Palet\Framework\Console\Exceptions\CommandNotFoundException;
use Palet\Framework\Console\Events\CommandResolving;
use Palet\Framework\Console\Events\CommandResolved;
use Palet\Framework\Console\Events\CommandExecuting;
use Palet\Framework\Console\Events\CommandExecuted;
use Palet\Framework\Console\Events\CommandFailed;

class CommandBus implements CommandBusInterface
{
    protected CommandResolverInterface $resolver;
    protected ?EventDispatcherInterface $events = null;
    protected array $middlewares = [];

    public function __construct(CommandResolverInterface $resolver)
    {
        $this->resolver = $resolver;
    }

    public function setEventDispatcher(EventDispatcherInterface $events): void
    {
        $this->events = $events;
    }

    public function addMiddleware($middleware): void
    {
        $this->middlewares[] = $middleware;
    }

    public function dispatch(string $commandName, InputInterface $input, OutputInterface $output): int
    {
        $this->dispatchResolvingEvent($commandName);

        $command = $this->resolver->resolve($commandName);

        if (!$command) {
            throw new CommandNotFoundException("Command \"{$commandName}\" is not defined.");
        }

        $this->dispatchResolvedEvent($commandName, $command);

        $pipeline = new CommandPipeline($input, $output);
        
        return $pipeline->send($command)
            ->through($this->middlewares)
            ->then(function ($cmd, $in, $out) use ($commandName) {
                
                $this->dispatchExecutingEvent($commandName, $cmd);

                try {
                    $exitCode = $cmd->run($in, $out);
                    
                    if ($exitCode === 0) {
                        $this->dispatchExecutedEvent($commandName, $cmd, $exitCode);
                    } else {
                        $this->dispatchFailedEvent($commandName, $cmd, $exitCode);
                    }
                    
                    return $exitCode;
                } catch (\Throwable $e) {
                    $this->dispatchFailedEvent($commandName, $cmd, 1, $e);
                    throw $e;
                }
            });
    }

    protected function dispatchResolvingEvent(string $name): void
    {
        if ($this->events) {
            $this->events->dispatch(new CommandResolving($name));
        }
    }

    protected function dispatchResolvedEvent(string $name, $command): void
    {
        if ($this->events) {
            $this->events->dispatch(new CommandResolved($name, $command));
        }
    }

    protected function dispatchExecutingEvent(string $name, $command): void
    {
        if ($this->events) {
            $this->events->dispatch(new CommandExecuting($name, $command));
        }
    }

    protected function dispatchExecutedEvent(string $name, $command, int $code): void
    {
        if ($this->events) {
            $this->events->dispatch(new CommandExecuted($name, $command, $code));
        }
    }

    protected function dispatchFailedEvent(string $name, $command, int $code, \Throwable $e = null): void
    {
        if ($this->events) {
            $this->events->dispatch(new CommandFailed($name, $command, $code, $e));
        }
    }
}
