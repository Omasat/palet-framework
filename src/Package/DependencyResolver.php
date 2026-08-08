<?php

declare(strict_types=1);

namespace Palet\Framework\Package;

use Palet\Framework\Contracts\Package\DependencyResolverInterface;
use Palet\Framework\Contracts\Package\PackageRepositoryInterface;

class DependencyResolver implements DependencyResolverInterface
{
    protected PackageRepositoryInterface $repository;
    protected SemVer $semver;
    protected array $resolved = [];
    protected array $resolving = [];

    public function __construct(PackageRepositoryInterface $repository, ?SemVer $semver = null)
    {
        $this->repository = $repository;
        $this->semver = $semver ?? new SemVer();
    }

    public function resolve(string $packageName, string $versionConstraint = '*'): array
    {
        $this->resolved = [];
        $this->resolving = [];
        
        $this->resolvePackage($packageName, $versionConstraint);
        
        return $this->resolved;
    }

    protected function resolvePackage(string $name, string $constraint): void
    {
        if (in_array($name, $this->resolving)) {
            throw new \RuntimeException("Circular dependency detected involving: {$name}");
        }

        if (isset($this->resolved[$name])) {
            // Check if existing resolved version satisfies new constraint
            if (!$this->semver->satisfies($this->resolved[$name]['version'], $constraint)) {
                throw new \RuntimeException("Version conflict for {$name}: Cannot satisfy {$constraint} alongside {$this->resolved[$name]['version']}");
            }
            return;
        }

        $this->resolving[] = $name;

        $package = $this->repository->find($name);
        
        if (!$package) {
            throw new \RuntimeException("Package not found: {$name}");
        }

        $availableVersions = $this->repository->getVersions($name);
        $selectedVersion = $this->selectHighestVersion($availableVersions, $constraint);
        
        if (!$selectedVersion) {
            throw new \RuntimeException("No version of {$name} satisfies constraint {$constraint}");
        }

        // Fake finding package metadata for the selected version
        // In reality, repository would return dependencies per version
        $dependencies = $package[$selectedVersion]['dependencies'] ?? [];

        foreach ($dependencies as $depName => $depConstraint) {
            $this->resolvePackage($depName, $depConstraint);
        }

        $this->resolved[$name] = [
            'name' => $name,
            'version' => $selectedVersion
        ];

        // Remove from resolving stack
        $this->resolving = array_diff($this->resolving, [$name]);
    }

    protected function selectHighestVersion(array $versions, string $constraint): ?string
    {
        usort($versions, fn($a, $b) => $this->semver->compare($b, $a)); // Sort descending
        
        foreach ($versions as $version) {
            if ($this->semver->satisfies($version, $constraint)) {
                return $version;
            }
        }
        
        return null;
    }
}
