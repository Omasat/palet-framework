<?php

declare(strict_types=1);

namespace Palet\Framework\Generator;

use Palet\Framework\Contracts\Generator\GeneratorInterface;
use Palet\Framework\Contracts\Generator\TemplateEngineInterface;
use Palet\Framework\Contracts\Events\EventDispatcherInterface;
use Palet\Framework\Generator\Events\GenerationStarted;
use Palet\Framework\Generator\Events\FileGenerating;
use Palet\Framework\Generator\Events\FileGenerated;
use Palet\Framework\Generator\Events\GenerationFailed;
use RuntimeException;

class CodeGenerator implements GeneratorInterface
{
    protected TemplateEngineInterface $engine;
    protected FileGenerator $fileGenerator;
    protected ?EventDispatcherInterface $events = null;

    public function __construct(TemplateEngineInterface $engine, FileGenerator $fileGenerator)
    {
        $this->engine = $engine;
        $this->fileGenerator = $fileGenerator;
    }

    public function setEventDispatcher(EventDispatcherInterface $events): void
    {
        $this->events = $events;
    }

    public function generate(GeneratorContext $context): bool
    {
        if ($this->events) {
            $this->events->dispatch(new GenerationStarted($context));
        }

        try {
            if (!file_exists($context->stubPath)) {
                throw new RuntimeException("Stub file not found at: {$context->stubPath}");
            }

            $stubContent = file_get_contents($context->stubPath);
            $compiledContent = $this->engine->compile($stubContent, $context->variables);

            if ($this->events) {
                $this->events->dispatch(new FileGenerating($context->destinationPath, $compiledContent));
            }

            $this->fileGenerator->generate(
                $context->destinationPath,
                $compiledContent,
                $context->force,
                $context->dryRun
            );

            if ($this->events) {
                $this->events->dispatch(new FileGenerated($context->destinationPath, $context->dryRun));
            }

            return true;

        } catch (\Throwable $e) {
            if ($this->events) {
                $this->events->dispatch(new GenerationFailed($context, $e));
            }
            throw $e;
        }
    }
}
