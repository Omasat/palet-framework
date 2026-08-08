<?php

declare(strict_types=1);

namespace Palet\Framework\Diagnostics\Checks;

use Palet\Framework\Contracts\Diagnostics\HealthCheckInterface;

class EnvironmentCheck implements HealthCheckInterface
{
    protected string $envPath;
    protected array $requiredKeys = ['APP_ENV', 'APP_KEY', 'DB_CONNECTION'];
    protected ?string $error = null;

    public function __construct(string $envPath)
    {
        $this->envPath = $envPath;
    }

    public function getName(): string
    {
        return 'Environment Configuration';
    }

    public function getDescription(): string
    {
        return 'Checks if the .env file exists and contains required keys.';
    }

    public function check(): bool
    {
        if (!file_exists($this->envPath)) {
            $this->error = '.env file is missing.';
            return false;
        }

        $content = file_get_contents($this->envPath);
        
        foreach ($this->requiredKeys as $key) {
            if (!str_contains($content, $key . '=')) {
                $this->error = "Missing required environment key: {$key}";
                return false;
            }
        }

        return true;
    }

    public function getErrorMessage(): ?string
    {
        return $this->error;
    }
}
