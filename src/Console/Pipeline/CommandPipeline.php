<?php

declare(strict_types=1);

namespace Palet\Framework\Console\Pipeline;

use Palet\Framework\Contracts\Console\CommandPipelineInterface;
use Palet\Framework\Contracts\Console\CommandInterface;
use Palet\Framework\Contracts\Console\InputInterface;
use Palet\Framework\Contracts\Console\OutputInterface;
use Closure;

class CommandPipeline implements CommandPipelineInterface
{
    protected CommandInterface $command;
    protected InputInterface $input;
    protected OutputInterface $output;
    protected array $middlewares = [];

    public function __construct(InputInterface $input, OutputInterface $output)
    {
        $this->input = $input;
        $this->output = $output;
    }

    public function send(CommandInterface $command): self
    {
        $this->command = $command;
        return $this;
    }

    public function through(array $middlewares): self
    {
        $this->middlewares = $middlewares;
        return $this;
    }

    public function then(Closure $destination): int
    {
        $pipeline = array_reduce(
            array_reverse($this->middlewares),
            $this->carry(),
            $this->prepareDestination($destination)
        );

        return $pipeline($this->command);
    }

    protected function prepareDestination(Closure $destination): Closure
    {
        return function ($command) use ($destination) {
            return $destination($command, $this->input, $this->output);
        };
    }

    protected function carry(): Closure
    {
        return function ($stack, $pipe) {
            return function ($command) use ($stack, $pipe) {
                if (is_string($pipe) && class_exists($pipe)) {
                    $pipe = new $pipe();
                }

                return $pipe->handle($command, $stack, $this->input, $this->output);
            };
        };
    }
}
