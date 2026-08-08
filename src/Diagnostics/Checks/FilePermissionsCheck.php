<?php

declare(strict_types=1);

namespace Palet\Framework\Diagnostics\Checks;

use Palet\Framework\Contracts\Diagnostics\HealthCheckInterface;

class FilePermissionsCheck implements HealthCheckInterface
{
    protected string $path;
    protected ?string $error = null;

    public function __construct(string $path)
    {
        $this->path = $path;
    }

    public function getName(): string
    {
        return 'File Permissions';
    }

    public function getDescription(): string
    {
        return 'Checks if crucial directories are writable.';
    }

    public function check(): bool
    {
        if (!file_exists($this->path)) {
            $this->error = "Directory does not exist: {$this->path}";
            return false;
        }

        if (!is_writable($this->path)) {
            $this->error = "Directory is not writable: {$this->path}";
            return false;
        }

        return true;
    }

    public function getErrorMessage(): ?string
    {
        return $this->error;
    }
}
