<?php

declare(strict_types=1);

namespace Palet\Framework\Foundation\Kernel;

use Palet\Framework\Contracts\Foundation\ApplicationInterface;
use Palet\Framework\Contracts\Foundation\Kernel\KernelInterface;
use Palet\Framework\Contracts\Foundation\Kernel\LifecycleInterface;
use Palet\Framework\Contracts\Foundation\Kernel\TerminableInterface;
use Throwable;

class Kernel implements KernelInterface
{
    protected ApplicationInterface $app;
    protected BootSequence $bootSequence;
    protected TerminationManager $terminationManager;
    protected RuntimeContext $context;
    
    protected KernelState $state = KernelState::Initializing;

    /**
     * @var array<int, class-string>
     */
    protected array $bootstrappers = [FoundationBootstrapperAdapter::class];

    public function __construct(ApplicationInterface $app)
    {
        $this->app = $app;
        $this->context = RuntimeContext::detect();
        $this->terminationManager = new TerminationManager();
        $this->bootSequence = new BootSequence($this->app, $this->bootstrappers);
        
        $this->registerShutdownHandler();
    }

    public function bootstrap(): void
    {
        if ($this->state !== KernelState::Initializing) {
            return;
        }

        try {
            $this->transitionState(KernelState::Bootstrapping);
            
            $this->checkRequirements();
            
            $this->bootSequence->run();
            
            $this->transitionState(KernelState::Ready);
        } catch (Throwable $e) {
            $this->transitionState(KernelState::Failed);
            throw $e;
        }
    }

    public function terminate(): void
    {
        if ($this->state === KernelState::Terminated) {
            return;
        }

        $this->transitionState(KernelState::ShuttingDown);
        
        $this->terminationManager->terminate();
        
        $this->transitionState(KernelState::Terminated);
    }

    public function getState(): string
    {
        return $this->state->value;
    }

    public function getApplication(): ApplicationInterface
    {
        return $this->app;
    }

    protected function transitionState(KernelState $newState): void
    {
        $oldState = $this->state;
        $this->state = $newState;
        
        if ($this->app->has(LifecycleInterface::class)) {
            $lifecycle = $this->app->make(LifecycleInterface::class);
            $lifecycle->onStateChange($oldState->value, $newState->value);
        }
    }

    protected function checkRequirements(): void
    {
        if (version_compare(PHP_VERSION, '8.2.0', '<')) {
            throw new \RuntimeException('Palet Framework requires PHP 8.2.0 or greater.');
        }
    }

    protected function registerShutdownHandler(): void
    {
        register_shutdown_function(function () {
            if ($this->state !== KernelState::Terminated) {
                $this->terminate();
            }
        });
    }
}
