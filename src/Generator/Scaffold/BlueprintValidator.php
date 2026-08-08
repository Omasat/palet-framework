<?php

declare(strict_types=1);

namespace Palet\Framework\Generator\Scaffold;

use Palet\Framework\Contracts\Generator\Scaffold\BlueprintInterface;
use InvalidArgumentException;

class BlueprintValidator
{
    public function validate(BlueprintInterface $blueprint): bool
    {
        $steps = $blueprint->getSteps();
        
        if (empty($steps)) {
            throw new InvalidArgumentException("Blueprint [{$blueprint->getName()}] has no steps defined.");
        }
        
        // Basic circular dependency check (naive approach for this sprint)
        $dependencies = $blueprint->getDependencies();
        foreach ($dependencies as $step => $deps) {
            foreach ($deps as $dep) {
                if ($dep === $step) {
                    throw new InvalidArgumentException("Blueprint [{$blueprint->getName()}] has a circular dependency on step: {$step}.");
                }
            }
        }

        return true;
    }
}
