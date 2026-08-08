<?php

declare(strict_types=1);

namespace Palet\Framework\Scaffold;

use Palet\Framework\Contracts\Scaffold\ProjectCreatorInterface;
use Palet\Framework\Contracts\Scaffold\ProjectValidatorInterface;
use Palet\Framework\Contracts\Events\EventDispatcherInterface;
use Palet\Framework\Scaffold\Templates\WebTemplate;
use Palet\Framework\Scaffold\Templates\ApiTemplate;
use Palet\Framework\Scaffold\Events\ProjectCreating;
use Palet\Framework\Scaffold\Events\ProjectCreated;
use Palet\Framework\Scaffold\Events\ProjectValidationCompleted;
use InvalidArgumentException;

class ProjectCreator implements ProjectCreatorInterface
{
    protected ProjectValidatorInterface $validator;
    protected DirectoryStructureBuilder $builder;
    protected EnvironmentInitializer $envInitializer;
    protected ApplicationBootstrapper $bootstrapper;
    protected ?EventDispatcherInterface $events = null;

    protected array $templates = [];

    public function __construct(
        ProjectValidatorInterface $validator,
        DirectoryStructureBuilder $builder,
        EnvironmentInitializer $envInitializer,
        ApplicationBootstrapper $bootstrapper
    ) {
        $this->validator = $validator;
        $this->builder = $builder;
        $this->envInitializer = $envInitializer;
        $this->bootstrapper = $bootstrapper;

        // Register default templates
        $this->registerTemplate(new WebTemplate());
        $this->registerTemplate(new ApiTemplate());
    }

    public function setEventDispatcher(EventDispatcherInterface $events): void
    {
        $this->events = $events;
    }

    public function registerTemplate($template): void
    {
        $this->templates[$template->getName()] = $template;
    }

    public function create(string $targetPath, string $templateName = 'web'): void
    {
        if (!isset($this->templates[$templateName])) {
            throw new InvalidArgumentException("Template [{$templateName}] not found.");
        }

        $this->validator->validate($targetPath);

        if ($this->events) {
            $this->events->dispatch(new ProjectValidationCompleted($targetPath));
            $this->events->dispatch(new ProjectCreating($targetPath, $templateName));
        }

        $template = $this->templates[$templateName];

        $this->builder->build($targetPath, $template);
        $this->envInitializer->initialize($targetPath);
        $this->bootstrapper->generate($targetPath);

        if ($this->events) {
            $this->events->dispatch(new ProjectCreated($targetPath, $templateName));
        }
    }
}
