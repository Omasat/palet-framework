<?php

declare(strict_types=1);

namespace Palet\Framework\Generator\Scaffold;

use Palet\Framework\Contracts\Generator\Scaffold\BlueprintRepositoryInterface;
use Palet\Framework\Contracts\Generator\Scaffold\BlueprintInterface;

class BlueprintRepository implements BlueprintRepositoryInterface
{
    protected array $blueprints = [];

    public function get(string $name): ?BlueprintInterface
    {
        return $this->blueprints[$name] ?? null;
    }

    public function all(): array
    {
        return $this->blueprints;
    }

    public function register(BlueprintInterface $blueprint): void
    {
        $this->blueprints[$blueprint->getName()] = $blueprint;
    }
}
