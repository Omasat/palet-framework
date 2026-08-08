<?php

declare(strict_types=1);

namespace Palet\Framework\Workflow;

class WorkflowManager
{
    protected array $definitions = [];

    public function __construct(protected WorkflowEngine $engine) {}

    public function registerDefinition(WorkflowDefinition $definition): void
    {
        $this->definitions[$definition->getId()] = $definition;
    }

    public function createInstance(string|int $definitionId): ?WorkflowInstance
    {
        if (!isset($this->definitions[$definitionId])) {
            return null;
        }

        return new WorkflowInstance(
            uniqid('wf_inst_'),
            $this->definitions[$definitionId]
        );
    }
    
    public function startInstance(WorkflowInstance $instance): void
    {
        $this->engine->start($instance);
    }
}
