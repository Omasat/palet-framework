<?php

declare(strict_types=1);

namespace Palet\Framework\Generator\Scaffold;

class ScaffoldContext
{
    protected string $blueprintName;
    protected array $options;
    protected array $generatedFiles = [];

    public function __construct(string $blueprintName, array $options = [])
    {
        $this->blueprintName = $blueprintName;
        $this->options = $options;
    }

    public function addGeneratedFile(string $path): void
    {
        $this->generatedFiles[] = $path;
    }

    public function getGeneratedFiles(): array
    {
        return $this->generatedFiles;
    }
    
    public function getBlueprintName(): string
    {
        return $this->blueprintName;
    }
    
    public function getOptions(): array
    {
        return $this->options;
    }
    
    public function isDryRun(): bool
    {
        return $this->options['dryRun'] ?? false;
    }
}
