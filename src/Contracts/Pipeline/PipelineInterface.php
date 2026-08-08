<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Pipeline;

use Closure;

interface PipelineInterface
{
    /**
     * Set the object being sent through the pipeline.
     */
    public function send(mixed $passable): self;

    /**
     * Set the array of pipes.
     */
    public function through(mixed $pipes): self;
    
    /**
     * Push an additional pipe onto the pipeline.
     */
    public function pipe(mixed $pipe): self;

    /**
     * Run the pipeline with a final destination callback.
     */
    public function then(Closure $destination): mixed;

    /**
     * Run the pipeline and return the result.
     */
    public function thenReturn(): mixed;
}
