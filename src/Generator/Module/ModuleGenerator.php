<?php

declare(strict_types=1);

namespace Palet\Framework\Generator\Module;

use Palet\Framework\Contracts\Generator\Module\ModuleGeneratorInterface;
use Palet\Framework\Contracts\Events\EventDispatcherInterface;
use Palet\Framework\Generator\Module\Events\ModuleCreating;
use Palet\Framework\Generator\Module\Events\ModuleCreated;

class ModuleGenerator implements ModuleGeneratorInterface
{
    protected ModuleStructureBuilder $structureBuilder;
    protected ModuleManifestGenerator $manifestGenerator;
    protected string $basePath;
    protected ?EventDispatcherInterface $events = null;

    public function __construct(
        ModuleStructureBuilder $structureBuilder,
        ModuleManifestGenerator $manifestGenerator,
        string $basePath
    ) {
        $this->structureBuilder = $structureBuilder;
        $this->manifestGenerator = $manifestGenerator;
        $this->basePath = $basePath;
    }

    public function setEventDispatcher(EventDispatcherInterface $events): void
    {
        $this->events = $events;
    }

    public function generate(string $name, array $options = []): void
    {
        $dryRun = $options['dryRun'] ?? false;

        if ($this->events) {
            $this->events->dispatch(new ModuleCreating($name));
        }

        $modulePath = rtrim($this->basePath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $name;

        $this->structureBuilder->build($name, $dryRun);
        $this->manifestGenerator->generate($name, $modulePath, $dryRun);

        if ($this->events) {
            $this->events->dispatch(new ModuleCreated($name, $modulePath));
        }
    }
}
