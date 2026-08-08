<?php

declare(strict_types=1);

namespace Palet\Framework\Generator\Entity;

use Palet\Framework\Contracts\Generator\Entity\EntityGeneratorInterface;
use Palet\Framework\Contracts\Generator\Entity\NamingConventionInterface;
use Palet\Framework\Generator\CodeGenerator;
use Palet\Framework\Generator\GeneratorContext;
use Palet\Framework\Contracts\Events\EventDispatcherInterface;
use Palet\Framework\Generator\Entity\Events\EntityGenerating;
use Palet\Framework\Generator\Entity\Events\EntityGenerated;
use Palet\Framework\Generator\Entity\Events\DomainCreated;

class EntityGenerator implements EntityGeneratorInterface
{
    protected CodeGenerator $codeGenerator;
    protected NamingConventionInterface $naming;
    protected DomainNamespaceResolver $namespaceResolver;
    protected ?EventDispatcherInterface $events = null;
    protected string $basePath;

    public function __construct(
        CodeGenerator $codeGenerator,
        NamingConventionInterface $naming,
        DomainNamespaceResolver $namespaceResolver,
        string $basePath
    ) {
        $this->codeGenerator = $codeGenerator;
        $this->naming = $naming;
        $this->namespaceResolver = $namespaceResolver;
        $this->basePath = $basePath;
    }

    public function setEventDispatcher(EventDispatcherInterface $events): void
    {
        $this->events = $events;
        $this->codeGenerator->setEventDispatcher($events);
    }

    public function generateBulk(string $entityName, array $options = []): void
    {
        $entityName = $this->naming->toPascalCase($entityName);
        $components = $options['components'] ?? ['entity'];
        $dryRun = $options['dryRun'] ?? false;
        $force = $options['force'] ?? false;

        foreach ($components as $component) {
            $this->generateComponent($entityName, $component, $dryRun, $force);
        }

        if ($this->events) {
            $this->events->dispatch(new DomainCreated($entityName, $components));
        }
    }

    protected function generateComponent(string $entityName, string $componentType, bool $dryRun, bool $force): void
    {
        if ($this->events) {
            $this->events->dispatch(new EntityGenerating($entityName, $componentType));
        }

        $namespace = $this->namespaceResolver->resolve($entityName, $componentType);
        $className = $this->getClassNameForComponent($entityName, $componentType);
        $destinationDir = $this->namespaceResolver->getPathForNamespace($namespace, $this->basePath);
        $destinationPath = $destinationDir . DIRECTORY_SEPARATOR . $className . '.php';

        $stubName = $componentType . '.stub';
        $stubPath = __DIR__ . '/Stubs/' . $stubName;

        $variables = [
            'Namespace' => $namespace,
            'ClassName' => $className,
            'EntityName' => $entityName,
            'EntityNameLower' => $this->naming->toCamelCase($entityName),
        ];

        // Specific variables for interfaces/implementations
        if ($componentType === 'repository') {
            $variables['InterfaceNamespace'] = $this->namespaceResolver->resolve($entityName, 'repository_interface');
            $variables['InterfaceName'] = $entityName . 'RepositoryInterface';
        }

        $context = new GeneratorContext($stubPath, $destinationPath, $variables, $force, $dryRun);
        
        $this->codeGenerator->generate($context);

        if ($this->events) {
            $this->events->dispatch(new EntityGenerated($entityName, $componentType, $destinationPath));
        }
    }

    protected function getClassNameForComponent(string $entityName, string $componentType): string
    {
        switch ($componentType) {
            case 'entity': return $entityName;
            case 'repository': return $entityName . 'Repository';
            case 'repository_interface': return $entityName . 'RepositoryInterface';
            case 'service': return $entityName . 'Service';
            case 'dto': return $entityName . 'DTO';
            default: return $entityName . ucfirst($componentType);
        }
    }
}
