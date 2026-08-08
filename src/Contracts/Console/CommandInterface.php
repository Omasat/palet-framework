<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Console;

interface CommandInterface
{
    /**
     * Execute the console command.
     */
    public function run(InputInterface $input, OutputInterface $output): int;

    /**
     * Get the command name.
     */
    public function getName(): string;
    
    /**
     * Get the command description.
     */
    public function getDescription(): string;
}
