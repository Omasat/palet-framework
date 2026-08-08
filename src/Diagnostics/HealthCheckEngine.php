<?php

declare(strict_types=1);

namespace Palet\Framework\Diagnostics;

use Palet\Framework\Contracts\Diagnostics\HealthCheckInterface;
use Palet\Framework\Contracts\Events\EventDispatcherInterface;
use Palet\Framework\Diagnostics\Events\HealthCheckPassed;
use Palet\Framework\Diagnostics\Events\HealthCheckFailed;
use Throwable;

class HealthCheckEngine
{
    /** @var HealthCheckInterface[] */
    protected array $checks = [];
    protected ?EventDispatcherInterface $events = null;

    public function register(HealthCheckInterface $check): void
    {
        $this->checks[] = $check;
    }
    
    public function setEventDispatcher(EventDispatcherInterface $events): void
    {
        $this->events = $events;
    }

    public function runAll(): array
    {
        $results = [];

        foreach ($this->checks as $check) {
            try {
                $passed = $check->check();
                $error = $passed ? null : $check->getErrorMessage();
                
                $results[] = [
                    'name' => $check->getName(),
                    'description' => $check->getDescription(),
                    'passed' => $passed,
                    'error' => $error
                ];

                if ($this->events) {
                    if ($passed) {
                        $this->events->dispatch(new HealthCheckPassed($check->getName()));
                    } else {
                        $this->events->dispatch(new HealthCheckFailed($check->getName(), $error));
                    }
                }
            } catch (Throwable $e) {
                $results[] = [
                    'name' => $check->getName(),
                    'description' => $check->getDescription(),
                    'passed' => false,
                    'error' => $e->getMessage()
                ];
                
                if ($this->events) {
                    $this->events->dispatch(new HealthCheckFailed($check->getName(), $e->getMessage()));
                }
            }
        }

        return $results;
    }
}
