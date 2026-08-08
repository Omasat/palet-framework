<?php

declare(strict_types=1);

namespace Palet\Framework\Workflow;

use Palet\Framework\Contracts\Workflow\WorkflowInterface;

class WorkflowDefinition implements WorkflowInterface
{
    protected array $steps = [];

    public function __construct(
        protected string|int $id,
        protected string $name,
        protected int $version = 1
    ) {}

    public function getId(): string|int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getVersion(): int
    {
        return $this->version;
    }

    public function addStep(string $stepId, array $config): self
    {
        $this->steps[$stepId] = $config;
        return $this;
    }

    public function getStep(string $stepId): ?array
    {
        return $this->steps[$stepId] ?? null;
    }
    
    public function getSteps(): array
    {
        return $this->steps;
    }
}
