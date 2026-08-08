<?php

declare(strict_types=1);

namespace Palet\Framework\Generator\Scaffold;

use Palet\Framework\Contracts\Generator\Scaffold\GenerationPipelineInterface;
use Palet\Framework\Generator\Scaffold\Events\FileGenerated;

class GenerationPipeline implements GenerationPipelineInterface
{
    protected ScaffoldContext $context;
    
    // In a real scenario, this would have injected handlers for each step type (e.g. ModuleHandler, EntityHandler)
    // For this sprint, we mock the execution of handlers.
    protected array $handlers = [];

    public function __construct(ScaffoldContext $context)
    {
        $this->context = $context;
    }
    
    public function registerHandler(string $step, callable $handler): void
    {
        $this->handlers[$step] = $handler;
    }

    public function process(array $steps, array $options = []): void
    {
        foreach ($steps as $step) {
            if (isset($this->handlers[$step])) {
                $handler = $this->handlers[$step];
                $generatedFiles = call_user_func($handler, $this->context);
                
                if (is_array($generatedFiles)) {
                    foreach ($generatedFiles as $file) {
                        $this->context->addGeneratedFile($file);
                    }
                }
            }
        }
    }
}
