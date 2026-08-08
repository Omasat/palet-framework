<?php

declare(strict_types=1);

namespace Palet\Framework\Generator\Scaffold;

use Palet\Framework\Contracts\Generator\Scaffold\ScaffoldOrchestratorInterface;
use Palet\Framework\Contracts\Generator\Scaffold\BlueprintRepositoryInterface;
use Palet\Framework\Contracts\Events\EventDispatcherInterface;
use Palet\Framework\Generator\Scaffold\Events\ScaffoldPlanning;
use Palet\Framework\Generator\Scaffold\Events\ScaffoldStarted;
use Palet\Framework\Generator\Scaffold\Events\BlueprintValidated;
use Palet\Framework\Generator\Scaffold\Events\GenerationCompleted;
use Palet\Framework\Generator\Scaffold\Events\ScaffoldFailed;
use RuntimeException;

class ScaffoldOrchestrator implements ScaffoldOrchestratorInterface
{
    protected BlueprintRepositoryInterface $repository;
    protected BlueprintValidator $validator;
    protected DependencyPlanner $planner;
    protected GenerationManifest $manifest;
    protected ?EventDispatcherInterface $events = null;

    public function __construct(
        BlueprintRepositoryInterface $repository,
        BlueprintValidator $validator,
        DependencyPlanner $planner,
        GenerationManifest $manifest
    ) {
        $this->repository = $repository;
        $this->validator = $validator;
        $this->planner = $planner;
        $this->manifest = $manifest;
    }

    public function setEventDispatcher(EventDispatcherInterface $events): void
    {
        $this->events = $events;
    }

    public function execute(string $blueprintName, array $options = []): void
    {
        try {
            if ($this->events) {
                $this->events->dispatch(new ScaffoldPlanning($blueprintName));
            }

            $blueprint = $this->repository->get($blueprintName);
            
            if (!$blueprint) {
                throw new RuntimeException("Blueprint [{$blueprintName}] not found.");
            }

            $this->validator->validate($blueprint);
            
            if ($this->events) {
                $this->events->dispatch(new BlueprintValidated($blueprintName));
            }

            $steps = $blueprint->getSteps();
            $dependencies = $blueprint->getDependencies();
            
            $executionOrder = $this->planner->plan($steps, $dependencies);

            $context = new ScaffoldContext($blueprintName, $options);
            $pipeline = clone ($options['pipeline'] ?? new GenerationPipeline($context));
            // In a real framework, Handlers would be injected via Container here
            
            if ($this->events) {
                $this->events->dispatch(new ScaffoldStarted($blueprintName, $executionOrder));
            }

            $pipeline->process($executionOrder, $options);
            
            if (!($options['dryRun'] ?? false)) {
                $this->manifest->record($blueprintName, $context->getGeneratedFiles());
            }

            if ($this->events) {
                $this->events->dispatch(new GenerationCompleted($blueprintName, $context->getGeneratedFiles()));
            }

        } catch (\Exception $e) {
            if ($this->events) {
                $this->events->dispatch(new ScaffoldFailed($blueprintName, $e->getMessage()));
            }
            if (!($options['dryRun'] ?? false)) {
                $this->manifest->recordFailure($blueprintName, $e->getMessage());
            }
            throw $e;
        }
    }
}
