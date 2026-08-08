<?php

declare(strict_types=1);

namespace Palet\Framework\Feature;

use Palet\Framework\Contracts\Feature\FeatureManagerInterface;
use Palet\Framework\Contracts\Feature\FeatureResolverInterface;
use Palet\Framework\Contracts\Feature\FeatureFlagInterface;
use Palet\Framework\Contracts\Events\EventDispatcherInterface;
use Palet\Framework\Feature\Events\FeatureEnabled;
use Palet\Framework\Feature\Events\FeatureDisabled;
use Palet\Framework\Feature\Events\FeatureEvaluated;

class FeatureManager implements FeatureManagerInterface
{
    protected array $registry = [];
    protected ?RuntimeFeatureContext $context = null;

    public function __construct(
        protected FeatureResolverInterface $resolver,
        protected ?EventDispatcherInterface $events = null
    ) {}

    public function setContext(RuntimeFeatureContext $context): void
    {
        $this->context = $context;
    }

    public function register(FeatureFlagInterface $feature): void
    {
        $this->registry[$feature->getName()] = $feature;
    }

    public function isActive(string $feature): bool
    {
        if (!isset($this->registry[$feature])) {
            return false;
        }

        $flag = $this->registry[$feature];
        
        $result = $this->resolver->resolve($flag, $this->context);
        
        if ($this->events) {
            $this->events->dispatch(new FeatureEvaluated($flag, $result));
        }

        return $result;
    }

    public function enable(string $feature): void
    {
        if (isset($this->registry[$feature])) {
            // Usually this would update persistence
            if ($this->events) {
                $this->events->dispatch(new FeatureEnabled($this->registry[$feature]));
            }
        }
    }

    public function disable(string $feature): void
    {
        if (isset($this->registry[$feature])) {
            // Usually this would update persistence
            if ($this->events) {
                $this->events->dispatch(new FeatureDisabled($this->registry[$feature]));
            }
        }
    }
}
