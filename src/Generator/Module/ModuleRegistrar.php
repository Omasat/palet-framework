<?php

declare(strict_types=1);

namespace Palet\Framework\Generator\Module;

use Palet\Framework\Contracts\Generator\Module\ModuleRegistrarInterface;

class ModuleRegistrar implements ModuleRegistrarInterface
{
    protected string $statusFilePath;

    public function __construct(string $statusFilePath)
    {
        $this->statusFilePath = $statusFilePath;
    }

    public function all(): array
    {
        if (!file_exists($this->statusFilePath)) {
            return [];
        }

        $content = file_get_contents($this->statusFilePath);
        $data = json_decode($content, true);

        return is_array($data) ? $data : [];
    }

    public function enable(string $name): bool
    {
        $modules = $this->all();
        $modules[$name] = true;
        return $this->save($modules);
    }

    public function disable(string $name): bool
    {
        $modules = $this->all();
        $modules[$name] = false;
        return $this->save($modules);
    }

    protected function save(array $modules): bool
    {
        return file_put_contents($this->statusFilePath, json_encode($modules, JSON_PRETTY_PRINT)) !== false;
    }
}
