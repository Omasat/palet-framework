<?php

declare(strict_types=1);

namespace Palet\Framework\Http\Middleware;

class MiddlewarePriorityResolver
{
    protected array $priorityMap = [];

    public function setPriorityMap(array $map): void
    {
        $this->priorityMap = $map;
    }

    public function sort(array $middleware): array
    {
        if (empty($this->priorityMap)) {
            return array_values(array_unique($middleware));
        }

        $priorityIndex = array_flip($this->priorityMap);

        $middleware = array_unique($middleware);
        
        usort($middleware, function ($a, $b) use ($priorityIndex) {
            $aIndex = $priorityIndex[$a] ?? 99999;
            $bIndex = $priorityIndex[$b] ?? 99999;
            
            return $aIndex <=> $bIndex;
        });

        return $middleware;
    }
}
