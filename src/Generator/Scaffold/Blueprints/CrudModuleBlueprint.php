<?php

declare(strict_types=1);

namespace Palet\Framework\Generator\Scaffold\Blueprints;

use Palet\Framework\Contracts\Generator\Scaffold\BlueprintInterface;

class CrudModuleBlueprint implements BlueprintInterface
{
    public function getName(): string
    {
        return 'crud_module';
    }

    public function getDescription(): string
    {
        return 'Generates a full CRUD module including Migration, Entity, and Controller';
    }

    public function getSteps(): array
    {
        return ['module', 'entity', 'migration', 'repository', 'controller'];
    }

    public function getDependencies(): array
    {
        return [
            'entity' => ['module'],
            'migration' => ['entity'],
            'repository' => ['entity'],
            'controller' => ['repository', 'entity']
        ];
    }
}
