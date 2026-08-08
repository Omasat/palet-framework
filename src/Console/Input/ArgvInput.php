<?php

declare(strict_types=1);

namespace Palet\Framework\Console\Input;

use Palet\Framework\Contracts\Console\InputInterface;

class ArgvInput implements InputInterface
{
    protected array $tokens;
    protected array $arguments = [];
    protected array $options = [];

    public function __construct(array $argv = null)
    {
        if (null === $argv) {
            $argv = $_SERVER['argv'] ?? [];
        }

        // Remove script name
        array_shift($argv);

        $this->tokens = $argv;
        $this->parse();
    }

    protected function parse(): void
    {
        foreach ($this->tokens as $token) {
            if (str_starts_with($token, '--')) {
                // Parse option (e.g. --force or --name=John)
                $this->parseLongOption(substr($token, 2));
            } elseif (str_starts_with($token, '-') && $token !== '-') {
                // Parse short option (e.g. -f or -abc)
                $this->parseShortOption(substr($token, 1));
            } else {
                // It's an argument
                $this->arguments[] = $token;
            }
        }
    }

    protected function parseLongOption(string $token): void
    {
        if (str_contains($token, '=')) {
            [$name, $value] = explode('=', $token, 2);
            $this->options[$name] = $value;
        } else {
            $this->options[$token] = true;
        }
    }

    protected function parseShortOption(string $token): void
    {
        // Simple implementation: treat each char as a boolean flag
        $chars = str_split($token);
        foreach ($chars as $char) {
            $this->options[$char] = true;
        }
    }

    public function getFirstArgument(): ?string
    {
        return $this->arguments[0] ?? null;
    }

    public function hasArgument(string $name): bool
    {
        // For simple index-based arguments for now (0, 1, 2)
        // In a real framework this maps to defined argument names
        return isset($this->arguments[(int)$name]);
    }

    public function getArgument(string $name): mixed
    {
        return $this->arguments[(int)$name] ?? null;
    }

    public function hasOption(string $name): bool
    {
        return array_key_exists($name, $this->options);
    }

    public function getOption(string $name): mixed
    {
        return $this->options[$name] ?? null;
    }
}
