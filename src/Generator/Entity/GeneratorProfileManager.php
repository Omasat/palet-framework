<?php

declare(strict_types=1);

namespace Palet\Framework\Generator\Entity;

use Palet\Framework\Contracts\Generator\Entity\GeneratorProfileInterface;

class GeneratorProfileManager
{
    protected array $profiles = [];

    public function register(GeneratorProfileInterface $profile): void
    {
        $this->profiles[$profile->getName()] = $profile;
    }

    public function getProfile(string $name): ?GeneratorProfileInterface
    {
        return $this->profiles[$name] ?? null;
    }
}

class DDDProfile implements GeneratorProfileInterface
{
    public function getName(): string
    {
        return 'ddd';
    }

    public function getDefaultComponents(): array
    {
        return ['entity', 'repository', 'repository_interface', 'service', 'dto'];
    }
}
