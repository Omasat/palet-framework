<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Generator\Scaffold;

interface ScaffoldOrchestratorInterface
{
    /**
     * Executes a blueprint to scaffold an application component.
     */
    public function execute(string $blueprintName, array $options = []): void;
}
