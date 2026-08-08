<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Console;

interface OutputInterface
{
    /**
     * Write a message to the output.
     */
    public function write(string|array $messages, bool $newline = false): void;

    /**
     * Write a message to the output and add a newline at the end.
     */
    public function writeln(string|array $messages): void;
}
