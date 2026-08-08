<?php

declare(strict_types=1);

namespace Palet\Framework\Asset;

use Palet\Framework\Contracts\Asset\DevServerInterface;

class DevServerResolver implements DevServerInterface
{
    protected string $hotFilePath;

    public function __construct(string $hotFilePath)
    {
        $this->hotFilePath = $hotFilePath;
    }

    public function isRunning(): bool
    {
        return file_exists($this->hotFilePath);
    }

    public function url(): ?string
    {
        if (!$this->isRunning()) {
            return null;
        }

        $url = trim(file_get_contents($this->hotFilePath));
        
        return $url !== '' ? $url : 'http://localhost:5173';
    }
}
