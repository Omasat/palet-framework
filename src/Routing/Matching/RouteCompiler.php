<?php

declare(strict_types=1);

namespace Palet\Framework\Routing\Matching;

use Palet\Framework\Contracts\Routing\RouteInterface;

class RouteCompiler
{
    const REGEX_DELIMITER = '#';

    /**
     * Compile the route's URI into a regular expression.
     */
    public function compile(RouteInterface $route): CompiledRoute
    {
        $uri = $route->getUri();
        $wheres = $route->getWheres();

        preg_match_all('/\{([a-zA-Z0-9_]+)\??\}/', $uri, $matches);
        $variables = $matches[1];

        $regex = $uri;

        foreach ($matches[0] as $index => $match) {
            $name = $matches[1][$index];
            $isOptional = str_ends_with($match, '?}');
            
            $constraint = $wheres[$name] ?? '[a-zA-Z0-9_-]+';
            
            // ReDoS protection using atomic groups if applicable, but for generic, we just use standard grouping
            $pattern = sprintf('(?P<%s>%s)', $name, $constraint);
            
            if ($isOptional) {
                $pattern = '(?:/' . $pattern . ')?';
                $regex = str_replace('/' . $match, $pattern, $regex);
                // Also handle the case where it's the only segment
                $regex = str_replace($match, $pattern, $regex);
            } else {
                $regex = str_replace($match, $pattern, $regex);
            }
        }

        $regex = self::REGEX_DELIMITER . '^' . $regex . '$' . self::REGEX_DELIMITER . 'su';

        return new CompiledRoute($regex, $variables);
    }
}
