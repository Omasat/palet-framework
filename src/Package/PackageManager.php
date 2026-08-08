<?php

declare(strict_types=1);

namespace Palet\Framework\Package;

use Palet\Framework\Contracts\Package\PackageManagerInterface;
use Palet\Framework\Contracts\Package\DependencyResolverInterface;
use Palet\Framework\Contracts\Events\EventDispatcherInterface;
use Palet\Framework\Package\Events\PackageInstalling;
use Palet\Framework\Package\Events\PackageInstalled;

class PackageManager implements PackageManagerInterface
{
    protected DependencyResolverInterface $resolver;
    protected ?EventDispatcherInterface $events = null;
    protected string $vendorPath;

    public function __construct(DependencyResolverInterface $resolver, string $vendorPath)
    {
        $this->resolver = $resolver;
        $this->vendorPath = $vendorPath;
    }

    public function setEventDispatcher(EventDispatcherInterface $events): void
    {
        $this->events = $events;
    }

    public function install(string $packageName, ?string $versionConstraint = null): void
    {
        $constraint = $versionConstraint ?? '*';
        
        // 1. Resolve Dependencies
        $resolvedPackages = $this->resolver->resolve($packageName, $constraint);
        
        // 2. Install each package (Mock logic for now)
        foreach ($resolvedPackages as $pkg) {
            $name = $pkg['name'];
            $version = $pkg['version'];

            if ($this->events) {
                $this->events->dispatch(new PackageInstalling($name, $version));
            }

            // Here we would download and extract
            $packagePath = $this->vendorPath . DIRECTORY_SEPARATOR . $name;
            if (!is_dir($packagePath)) {
                mkdir($packagePath, 0755, true);
            }
            
            // Mock manifest creation
            file_put_contents($packagePath . '/palet.json', json_encode([
                'name' => $name,
                'version' => $version
            ]));

            if ($this->events) {
                $this->events->dispatch(new PackageInstalled($name, $version, $packagePath));
            }
        }
    }

    public function remove(string $packageName): void
    {
        // Implementation for removing packages
    }

    public function update(string $packageName): void
    {
        // Implementation for updating packages
    }
}
