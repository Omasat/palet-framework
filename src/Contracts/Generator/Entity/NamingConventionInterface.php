<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Generator\Entity;

interface NamingConventionInterface
{
    public function toPlural(string $name): string;
    public function toSingular(string $name): string;
    public function toCamelCase(string $name): string;
    public function toSnakeCase(string $name): string;
    public function toKebabCase(string $name): string;
    public function toPascalCase(string $name): string;
}
