<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Console;

interface KernelInterface
{
    /**
     * Handle an incoming console command.
     */
    public function handle(InputInterface $input, ?OutputInterface $output = null): int;

    /**
     * Terminate the application.
     */
    public function terminate(InputInterface $input, int $status): void;
}
