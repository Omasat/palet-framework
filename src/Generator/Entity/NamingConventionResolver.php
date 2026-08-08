<?php

declare(strict_types=1);

namespace Palet\Framework\Generator\Entity;

use Palet\Framework\Contracts\Generator\Entity\NamingConventionInterface;

class NamingConventionResolver implements NamingConventionInterface
{
    public function toPlural(string $name): string
    {
        // A very basic naive pluralizer for demo purposes.
        if (str_ends_with($name, 'y')) {
            return substr($name, 0, -1) . 'ies';
        }
        if (str_ends_with($name, 's')) {
            return $name . 'es';
        }
        return $name . 's';
    }

    public function toSingular(string $name): string
    {
        // A very basic naive singularizer.
        if (str_ends_with($name, 'ies')) {
            return substr($name, 0, -3) . 'y';
        }
        if (str_ends_with($name, 's')) {
            return rtrim($name, 's');
        }
        return $name;
    }

    public function toCamelCase(string $name): string
    {
        return lcfirst($this->toPascalCase($name));
    }

    public function toSnakeCase(string $name): string
    {
        $name = preg_replace('/\s+/u', '', ucwords($name));
        $name = preg_replace('/(.)(?=[A-Z])/u', '$1_', $name);
        return strtolower($name);
    }

    public function toKebabCase(string $name): string
    {
        $name = preg_replace('/\s+/u', '', ucwords($name));
        $name = preg_replace('/(.)(?=[A-Z])/u', '$1-', $name);
        return strtolower($name);
    }

    public function toPascalCase(string $name): string
    {
        $name = str_replace(['_', '-'], ' ', $name);
        return str_replace(' ', '', ucwords($name));
    }
}
