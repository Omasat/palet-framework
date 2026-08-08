<?php

declare(strict_types=1);

namespace Palet\Framework\Generator\Entity;

class DomainNamespaceResolver
{
    protected string $baseNamespace;

    public function __construct(string $baseNamespace = 'App\\Domain')
    {
        $this->baseNamespace = rtrim($baseNamespace, '\\');
    }

    public function resolve(string $entityName, string $componentType): string
    {
        // e.g. App\Domain\User\Entities
        $pluralComponent = $this->pluralizeComponent($componentType);
        return $this->baseNamespace . '\\' . $entityName . '\\' . $pluralComponent;
    }

    public function getPathForNamespace(string $namespace, string $basePath): string
    {
        // Convert App\Domain\... to app/Domain/...
        $relative = str_replace('App\\', '', $namespace);
        $relative = str_replace('\\', DIRECTORY_SEPARATOR, $relative);
        
        return rtrim($basePath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $relative;
    }

    protected function pluralizeComponent(string $component): string
    {
        $map = [
            'entity' => 'Entities',
            'repository' => 'Repositories',
            'repository_interface' => 'Repositories',
            'service' => 'Services',
            'dto' => 'DTOs',
            'value_object' => 'ValueObjects',
        ];

        return $map[$component] ?? ucfirst($component) . 's';
    }
}
