<?php

declare(strict_types=1);

namespace Palet\Framework\Generator\Scaffold\Blueprints;

use Palet\Framework\Contracts\Generator\Scaffold\BlueprintInterface;

class BasicEntityBlueprint implements BlueprintInterface
{
    public function getName(): string
    {
        return 'basic_entity';
    }

    public function getDescription(): string
    {
        return 'Generates a Basic Entity and Repository';
    }

    public function getSteps(): array
    {
        return ['entity', 'repository_interface', 'repository'];
    }

    public function getDependencies(): array
    {
        return [
            'repository' => ['repository_interface', 'entity'],
        ];
    }
}
