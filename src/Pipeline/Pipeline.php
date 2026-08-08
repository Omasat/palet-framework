<?php

declare(strict_types=1);

namespace Palet\Framework\Pipeline;

use Palet\Framework\Contracts\Pipeline\PipelineInterface;
use Palet\Framework\Contracts\Foundation\ApplicationInterface;
use Closure;
use RuntimeException;

class Pipeline implements PipelineInterface
{
    protected ?ApplicationInterface $app;
    protected PipeResolver $resolver;
    
    protected mixed $passable;
    protected array $pipes = [];
    protected string $method = 'handle';

    public function __construct(?ApplicationInterface $app = null, ?PipeResolver $resolver = null)
    {
        $this->app = $app;
        $this->resolver = $resolver ?? new PipeResolver($app);
    }

    public function send(mixed $passable): self
    {
        $this->passable = $passable;
        return $this;
    }

    public function through(mixed $pipes): self
    {
        $this->pipes = is_array($pipes) ? $pipes : func_get_args();
        return $this;
    }
    
    public function pipe(mixed $pipe): self
    {
        $this->pipes[] = $pipe;
        return $this;
    }

    public function via(string $method): self
    {
        $this->method = $method;
        return $this;
    }

    public function then(Closure $destination): mixed
    {
        $pipeline = array_reduce(
            array_reverse($this->pipes),
            $this->carry(),
            $this->prepareDestination($destination)
        );

        return $pipeline($this->passable);
    }

    public function thenReturn(): mixed
    {
        return $this->then(function ($passable) {
            return $passable;
        });
    }

    protected function prepareDestination(Closure $destination): Closure
    {
        return function ($passable) use ($destination) {
            return $destination($passable);
        };
    }

    protected function carry(): Closure
    {
        return function ($stack, $pipe) {
            return function ($passable) use ($stack, $pipe) {
                if (is_callable($pipe)) {
                    // Callable pipe can be a Closure directly handling the logic
                    return $pipe($passable, $stack);
                }
                
                [$resolvedPipe, $parameters] = $this->resolver->resolve($pipe);

                if (is_callable($resolvedPipe)) {
                    return $resolvedPipe($passable, $stack, ...$parameters);
                }

                $parameters = array_merge([$passable, $stack], $parameters);
                
                return $resolvedPipe->{$this->method}(...$parameters);
            };
        };
    }
}
