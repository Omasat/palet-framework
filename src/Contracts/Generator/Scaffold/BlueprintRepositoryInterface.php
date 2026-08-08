<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Generator\Scaffold;

interface BlueprintRepositoryInterface
{
    public function get(string $name): ?BlueprintInterface;
    public function all(): array;
    public function register(BlueprintInterface $blueprint): void;
}
