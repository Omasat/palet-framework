<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Console;

use Closure;

interface CommandMiddlewareInterface
{
    /**
     * Process an incoming command before execution.
     */
    public function handle(CommandInterface $command, Closure $next, InputInterface $input, OutputInterface $output): int;
}
