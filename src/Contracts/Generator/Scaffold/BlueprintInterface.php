<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Generator\Scaffold;

interface BlueprintInterface
{
    public function getName(): string;
    public function getDescription(): string;
    
    /**
     * Get the steps to generate. Each step defines the component to generate (e.g. 'module', 'entity', 'migration')
     */
    public function getSteps(): array;
    
    /**
     * Get the dependencies between steps. (e.g. ['entity' => ['module'], 'migration' => ['entity']])
     */
    public function getDependencies(): array;
}
