<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Console;

use Closure;

interface CommandPipelineInterface
{
    /**
     * Send a command through the pipeline.
     */
    public function send(CommandInterface $command): self;

    /**
     * Set the middlewares for the pipeline.
     */
    public function through(array $middlewares): self;

    /**
     * Run the pipeline with a final destination callback.
     */
    public function then(Closure $destination): int;
}
