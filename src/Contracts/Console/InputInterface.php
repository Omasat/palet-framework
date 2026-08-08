<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Console;

interface InputInterface
{
    /**
     * Get the first argument from the raw parameters (not parsed).
     */
    public function getFirstArgument(): ?string;

    /**
     * Determine if the input contains a specific argument.
     */
    public function hasArgument(string $name): bool;

    /**
     * Get the value of a command argument.
     */
    public function getArgument(string $name): mixed;

    /**
     * Determine if the input contains a specific option.
     */
    public function hasOption(string $name): bool;

    /**
     * Get the value of a command option.
     */
    public function getOption(string $name): mixed;
}
