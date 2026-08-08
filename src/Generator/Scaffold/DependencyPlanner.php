<?php

declare(strict_types=1);

namespace Palet\Framework\Generator\Scaffold;

use RuntimeException;

class DependencyPlanner
{
    public function plan(array $steps, array $dependencies): array
    {
        $order = [];
        $visited = [];
        $processing = [];

        $visit = function($step) use (&$visit, &$order, &$visited, &$processing, $dependencies, $steps) {
            if (!in_array($step, $steps)) {
                return; // Ignore steps not in the current generation request
            }
            if (isset($processing[$step])) {
                throw new RuntimeException("Circular dependency detected involving step: {$step}");
            }
            if (isset($visited[$step])) {
                return;
            }

            $processing[$step] = true;

            $deps = $dependencies[$step] ?? [];
            foreach ($deps as $dep) {
                $visit($dep);
            }

            unset($processing[$step]);
            $visited[$step] = true;
            $order[] = $step;
        };

        foreach ($steps as $step) {
            $visit($step);
        }

        return $order;
    }
}
